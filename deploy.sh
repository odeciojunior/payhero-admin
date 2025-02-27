#!/bin/bash
#
# Script de implantação/redeploy do Admin
#

# Faz o script parar em caso de qualquer erro.
set -e

# Configuração de logging
LOG_FILE="/var/log/deploy.log"
echo "==> Deploy iniciado em $(date)" | tee -a "$LOG_FILE"

# Validar ambiente
echo "==> Validando ambiente..." | tee -a "$LOG_FILE"
if [[ $EUID -ne 0 ]]; then
   echo "Este script deve ser executado como root" | tee -a "$LOG_FILE"
   exit 1
fi

# Adicionar trap para capturar erros
trap 'echo "Erro na linha $LINENO. Comando: $BASH_COMMAND Status: $?" | tee -a "$LOG_FILE"' ERR

# Variáveis importantes
EFS_SERVER="fs-088340380da8ef1cf.efs.us-east-1.amazonaws.com:/"
EFS_MOUNTPOINT="/var/www/html/efs-admin-azc-prd"
ADMIN_DIR="/var/www/html/admin.azcend.com.br"
ADMIN_USER="ubuntu"
ADMIN_GROUP="www-data"

# Configuração do NFS com lock para suporte adequado ao Laravel
MOUNT_OPTS="nfsvers=4.1,rsize=1048576,wsize=1048576,hard,timeo=600,retrans=2,noresvport"
FSTAB_ENTRY="$EFS_SERVER $EFS_MOUNTPOINT nfs defaults,_netdev 0 0"

# -------------------------------------------------------------
# Funções auxiliares
# -------------------------------------------------------------

# Função para verificar diretórios
verify_directory() {
    local dir=$1
    if [ ! -d "$dir" ]; then
        echo "ERRO: Diretório $dir não existe!" | tee -a "$LOG_FILE"
        return 1
    fi
    echo "Diretório $dir existe e está acessível" | tee -a "$LOG_FILE"
}

# Função para verificar link simbólico do storage
verify_storage_link() {
    if [ ! -L "$ADMIN_DIR/storage" ]; then
        echo "ERRO: Link simbólico do storage não existe!" | tee -a "$LOG_FILE"
        return 1
    fi
    echo "Link simbólico do storage está correto" | tee -a "$LOG_FILE"
}

# Função para verificar permissões
verify_permissions() {
    local dir=$1
    echo "Verificando permissões em $dir:" | tee -a "$LOG_FILE"
    ls -la "$dir" >> "$LOG_FILE" 2>&1
}

# Função para verificar e criar diretórios se não existirem
create_directory_if_not_exists() {
    local dir=$1
    if [ ! -d "$dir" ]; then
        echo "==> Criando diretório $dir..." | tee -a "$LOG_FILE"
        mkdir -p "$dir"
        if [[ "$dir" == *"storage"* ]]; then
            chown www-data:www-data "$dir"
            chmod 777 "$dir"
        else
            chown $ADMIN_USER:$ADMIN_GROUP "$dir"
            chmod 2777 "$dir"
        fi
        verify_directory "$dir"
    fi
}

# Função para ajustar permissões de diretório
set_permissions() {
    local dir=$1
    echo "==> Ajustando permissões em $dir..." | tee -a "$LOG_FILE"
    
    if [[ "$dir" == *"storage"* ]]; then
        # Permissões específicas para pasta storage
        chown -R www-data:www-data "$dir"
        chmod -R 777 "$dir"
        
        # Garantir permissões em diretórios críticos do storage
        for subdir in framework/cache framework/sessions framework/views logs; do
            if [ -d "$dir/$subdir" ]; then
                chown -R www-data:www-data "$dir/$subdir"
                chmod -R 777 "$dir/$subdir"
            fi
        done
    else
        # Permissões padrão para outros diretórios
        chown -R $ADMIN_USER:$ADMIN_GROUP "$dir"
        find "$dir" -type d -exec chmod 2777 {} \;
        find "$dir" -type f -exec chmod 666 {} \;
    fi
    
    verify_permissions "$dir"
}

# Função para garantir que o diretório EFS está desmontado
ensure_efs_unmounted() {
    echo "==> Verificando e desmontando EFS se necessário..." | tee -a "$LOG_FILE"
    
    if lsof "$EFS_MOUNTPOINT" 2>/dev/null; then
        echo "Finalizando processos usando $EFS_MOUNTPOINT" | tee -a "$LOG_FILE"
        lsof "$EFS_MOUNTPOINT" | awk 'NR>1 {print $2}' | sort -u | xargs -r kill -9
        sleep 2
    fi

    if mountpoint -q "$EFS_MOUNTPOINT"; then
        echo "Desmontando $EFS_MOUNTPOINT" | tee -a "$LOG_FILE"
        umount -f "$EFS_MOUNTPOINT" || umount -l "$EFS_MOUNTPOINT"
        sleep 2
    fi
}

# Função para montar EFS
mount_efs() {
    echo "==> Montando EFS..." | tee -a "$LOG_FILE"
    create_directory_if_not_exists "$EFS_MOUNTPOINT"
    
    mount -t nfs4 -o "$MOUNT_OPTS" "$EFS_SERVER" "$EFS_MOUNTPOINT" || {
        echo "Erro ao montar EFS. Abortando." | tee -a "$LOG_FILE"
        exit 1
    }
    
    echo "EFS montado com sucesso em $EFS_MOUNTPOINT" | tee -a "$LOG_FILE"
}

# Função para verificar serviços
verify_services() {
    echo "==> Verificando status dos serviços..." | tee -a "$LOG_FILE"
    
    for service in nginx php8.2-fpm; do
        if ! systemctl is-active --quiet $service; then
            echo "ERRO: $service não está rodando!" | tee -a "$LOG_FILE"
            systemctl status $service >> "$LOG_FILE"
            return 1
        fi
    done
    
    echo "Todos os serviços estão rodando corretamente." | tee -a "$LOG_FILE"
    return 0
}

# Função para criar arquivos PHP utilitários
create_php_utilities() {
    echo "==> Criando arquivos PHP utilitários..." | tee -a "$LOG_FILE"
    
    RANDOM_SUFFIX=$(openssl rand -hex 8)
    PHPINFO_FILE="$ADMIN_DIR/public/info_${RANDOM_SUFFIX}.php"
    OPCACHE_FILE="$ADMIN_DIR/public/reset_opcache_${RANDOM_SUFFIX}.php"
    
    echo "<?php phpinfo();" | sudo tee "$PHPINFO_FILE" > /dev/null
    echo "<?php opcache_reset(); echo 'OPcache reset successfully.';" | sudo tee "$OPCACHE_FILE" > /dev/null
    
    chown www-data:www-data "$PHPINFO_FILE" "$OPCACHE_FILE"
    chmod 666 "$PHPINFO_FILE" "$OPCACHE_FILE"
    
    echo "Arquivos PHP criados com sucesso:" | tee -a "$LOG_FILE"
    echo "- PHPInfo: info_${RANDOM_SUFFIX}.php" | tee -a "$LOG_FILE"
    echo "- OPcache Reset: reset_opcache_${RANDOM_SUFFIX}.php" | tee -a "$LOG_FILE"
    
    sudo php "$OPCACHE_FILE" | tee -a "$LOG_FILE"
    verify_permissions "$ADMIN_DIR/public"
}

# Função para limpar e recriar cache do Laravel
refresh_laravel_cache() {
    echo "==> Limpando cache do Laravel..." | tee -a "$LOG_FILE"
    cd "$ADMIN_DIR" || {
        echo "Erro ao acessar diretório $ADMIN_DIR" | tee -a "$LOG_FILE"
        return 1
    }

    local commands=(
        "php artisan cache:clear"
        "php artisan route:clear"
        "php artisan view:clear"
        "php artisan clear-compiled"
        "composer dump-autoload -o"
        "php artisan route:cache"
        "php artisan view:cache"
        "php artisan config:clear"
    )

    for cmd in "${commands[@]}"; do
        echo "Executando: $cmd" | tee -a "$LOG_FILE"
        sudo -H -u www-data bash -c "cd $ADMIN_DIR && $cmd" || {
            echo "AVISO: Falha ao executar $cmd" | tee -a "$LOG_FILE"
        }
    done
}

# Função para limpar arquivos PHP temporários
clean_temporary_files() {
    echo "==> Limpando arquivos temporários no diretório public/..." | tee -a "$LOG_FILE"
    
    local temp_files=$(find "$ADMIN_DIR/public" -type f \( -name "info_*.php" -o -name "reset_opcache_*.php" \))
    if [ -n "$temp_files" ]; then
        echo "Arquivos encontrados para remoção:" | tee -a "$LOG_FILE"
        echo "$temp_files" | tee -a "$LOG_FILE"
        find "$ADMIN_DIR/public" -type f \( -name "info_*.php" -o -name "reset_opcache_*.php" \) -exec rm -f {} \;
        echo "Arquivos temporários removidos com sucesso." | tee -a "$LOG_FILE"
    else
        echo "Nenhum arquivo temporário encontrado para remoção." | tee -a "$LOG_FILE"
    fi
}

# -------------------------------------------------------------
# Etapas do Deploy
# -------------------------------------------------------------

# 1. Parar serviços
echo "==> Parando serviços..." | tee -a "$LOG_FILE"
systemctl stop nginx php8.2-fpm || true
sleep 2

# 2. Desmontar e remontar EFS
ensure_efs_unmounted
mount_efs

# Verifica se a montagem foi bem-sucedida
if ! mountpoint -q "$EFS_MOUNTPOINT"; then
    echo "Falha na montagem do EFS. Abortando." | tee -a "$LOG_FILE"
    exit 1
fi

# 3. Criar estrutura de diretórios
echo "==> Criando estrutura de diretórios..." | tee -a "$LOG_FILE"
for dir in storage storage/logs storage/framework storage/framework/cache storage/framework/sessions storage/framework/views nginx; do
    create_directory_if_not_exists "$EFS_MOUNTPOINT/$dir"
done

# Ajuste final das permissões do storage
chown -R www-data:www-data "$EFS_MOUNTPOINT/storage"
chmod -R 777 "$EFS_MOUNTPOINT/storage"

# 4. Ajuste de permissões antes de atualizar o código via Git
echo "==> Ajustando permissões no projeto antes do Git Pull..." | tee -a "$LOG_FILE"
cd "$ADMIN_DIR"

chown -R $ADMIN_USER:$ADMIN_GROUP .
find . -type d -exec chmod 777 {} \;
find . -type f -exec chmod 666 {} \;

# Ajustar permissões executáveis para scripts críticos
[ -f artisan ] && chmod +x artisan
[ -f deploy.sh ] && chmod +x deploy.sh

# 4.2. Atualizar código via Git
echo "==> Atualizando código via Git..." | tee -a "$LOG_FILE"
sudo -H -u $ADMIN_USER bash -c "git reset --hard origin/main"
sudo -H -u $ADMIN_USER bash -c "git pull"

# 5. Configurar storage
echo "==> Configurando storage..." | tee -a "$LOG_FILE"
rm -rf "$ADMIN_DIR/storage"
ln -sf "$EFS_MOUNTPOINT/storage" "$ADMIN_DIR/storage"

# Aplicar permissões específicas no storage do EFS
chown -R www-data:www-data "$EFS_MOUNTPOINT/storage"
chmod -R 777 "$EFS_MOUNTPOINT/storage"

verify_storage_link || {
    echo "Tentando recriar link do storage..." | tee -a "$LOG_FILE"
    rm -rf "$ADMIN_DIR/storage"
    ln -sf "$EFS_MOUNTPOINT/storage" "$ADMIN_DIR/storage"
    verify_storage_link
}

# 6. Configurar .env
echo "==> Configurando .env..." | tee -a "$LOG_FILE"
if [ -f "$EFS_MOUNTPOINT/.env" ]; then
    cp "$EFS_MOUNTPOINT/.env" "$ADMIN_DIR/.env"
    chown www-data:www-data "$ADMIN_DIR/.env"
    chmod 666 "$ADMIN_DIR/.env"
fi

# 7. Ajustar permissões críticas
echo "==> Ajustando permissões críticas..." | tee -a "$LOG_FILE"
set_permissions "$ADMIN_DIR/bootstrap/cache"
set_permissions "$EFS_MOUNTPOINT/storage"

# 8. Reiniciar serviços
echo "==> Reiniciando serviços..." | tee -a "$LOG_FILE"
systemctl start php8.2-fpm
sleep 2
systemctl start nginx
sleep 2

# 9. Verificar serviços
verify_services || {
    echo "ERRO: Falha na verificação dos serviços. Verifique os logs." | tee -a "$LOG_FILE"
    exit 1
}

# 10. Verificar montagem automática
if ! grep -q "$EFS_SERVER" /etc/fstab; then
    echo "$FSTAB_ENTRY" >> /etc/fstab
    echo "Entrada adicionada ao fstab." | tee -a "$LOG_FILE"
fi

# 11. Criar arquivos PHP utilitários e limpar temporários
create_php_utilities
clean_temporary_files
php -r "opcache_reset();" | tee -a "$LOG_FILE"

# 12. Atualizar cache do Laravel
refresh_laravel_cache

# 13. Subir artisan
php artisan up

# 14. Publicar Horizon
echo "==> Publicando Horizon..." | tee -a "$LOG_FILE"
sudo -H -u www-data bash -c "cd $ADMIN_DIR && php artisan horizon:publish"

# 15. Atualizar script de deploy no EFS
echo "==> Atualizando script de deploy no EFS..." | tee -a