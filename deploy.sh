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
        chown $ADMIN_USER:$ADMIN_GROUP "$dir"
        chmod 2775 "$dir"
        verify_directory "$dir"
    fi
}

# Função para ajustar permissões de diretório
set_permissions() {
    local dir=$1
    echo "==> Ajustando permissões em $dir..." | tee -a "$LOG_FILE"
    
    # Configurar ownership
    chown -R $ADMIN_USER:$ADMIN_GROUP "$dir"
    
    # Configurar permissões base (diretórios = 2775, arquivos = 664)
    find "$dir" -type d -exec chmod 2775 {} \;  # SGID para diretórios
    find "$dir" -type f -exec chmod 664 {} \;   # Permissões para arquivos
    
    # Garantir SGID em diretórios críticos (se existirem)
    chmod 2775 "$dir"
    [ -d "$dir/framework" ] && chmod 2775 "$dir/framework"
    [ -d "$dir/framework/cache" ] && chmod 2775 "$dir/framework/cache"
    [ -d "$dir/framework/sessions" ] && chmod 2775 "$dir/framework/sessions"
    [ -d "$dir/framework/views" ] && chmod 2775 "$dir/framework/views"
    [ -d "$dir/logs" ] && chmod 2775 "$dir/logs"
    
    verify_permissions "$dir"
}

# Função para garantir que o diretório EFS está desmontado
ensure_efs_unmounted() {
    echo "==> Verificando e desmontando EFS se necessário..." | tee -a "$LOG_FILE"
    
    # Verifica se existem processos usando o EFS
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
    
    if ! systemctl is-active --quiet nginx; then
        echo "ERRO: Nginx não está rodando!" | tee -a "$LOG_FILE"
        systemctl status nginx >> "$LOG_FILE"
        return 1
    fi
    
    if ! systemctl is-active --quiet php8.2-fpm; then
        echo "ERRO: PHP-FPM não está rodando!" | tee -a "$LOG_FILE"
        systemctl status php8.2-fpm >> "$LOG_FILE"
        return 1
    fi
    
    echo "Todos os serviços estão rodando corretamente." | tee -a "$LOG_FILE"
    return 0
}

# Função para criar arquivos PHP utilitários
create_php_utilities() {
    echo "==> Criando arquivos PHP utilitários..." | tee -a "$LOG_FILE"
    
    # Gerar sufixo aleatório
    RANDOM_SUFFIX=$(openssl rand -hex 8)
    
    # Definir caminhos dos arquivos
    PHPINFO_FILE="$ADMIN_DIR/public/info_${RANDOM_SUFFIX}.php"
    OPCACHE_FILE="$ADMIN_DIR/public/reset_opcache_${RANDOM_SUFFIX}.php"
    
    # Criar arquivos
    echo "<?php phpinfo();" | sudo tee "$PHPINFO_FILE" > /dev/null
    echo "<?php opcache_reset(); echo 'OPcache reset successfully.';" | sudo tee "$OPCACHE_FILE" > /dev/null
    
    # Ajustar permissões
    chown $ADMIN_USER:$ADMIN_GROUP "$PHPINFO_FILE" "$OPCACHE_FILE"
    chmod 644 "$PHPINFO_FILE" "$OPCACHE_FILE"
    
    echo "Arquivos PHP criados com sucesso:" | tee -a "$LOG_FILE"
    echo "- PHPInfo: info_${RANDOM_SUFFIX}.php" | tee -a "$LOG_FILE"
    echo "- OPcache Reset: reset_opcache_${RANDOM_SUFFIX}.php" | tee -a "$LOG_FILE"
    
    # Teste imediato do arquivo de reset de OPcache
    sudo php "$OPCACHE_FILE" | tee -a "$LOG_FILE"
    
    # Verificar arquivos
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
        sudo -H -u $ADMIN_GROUP bash -c "cd $ADMIN_DIR && $cmd" || {
            echo "AVISO: Falha ao executar $cmd" | tee -a "$LOG_FILE"
        }
    done
}

# Função para limpar arquivos PHP temporários
clean_temporary_files() {
    echo "==> Limpando arquivos temporários no diretório public/..." | tee -a "$LOG_FILE"
    
    # Listar os arquivos que serão removidos
    local temp_files=$(find "$ADMIN_DIR/public" -type f \( -name "info_*.php" -o -name "reset_opcache_*.php" \))
    if [ -n "$temp_files" ]; then
        echo "Arquivos encontrados para remoção:" | tee -a "$LOG_FILE"
        echo "$temp_files" | tee -a "$LOG_FILE"
        
        # Remover os arquivos
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
create_directory_if_not_exists "$EFS_MOUNTPOINT/storage"
create_directory_if_not_exists "$EFS_MOUNTPOINT/storage/logs"
create_directory_if_not_exists "$EFS_MOUNTPOINT/storage/framework"
create_directory_if_not_exists "$EFS_MOUNTPOINT/storage/framework/cache"
create_directory_if_not_exists "$EFS_MOUNTPOINT/storage/framework/sessions"
create_directory_if_not_exists "$EFS_MOUNTPOINT/storage/framework/views"
create_directory_if_not_exists "$EFS_MOUNTPOINT/nginx"

# 4. Ajuste de permissões antes de atualizar o código via Git
echo "==> Ajustando permissões no projeto antes do Git Pull..." | tee -a "$LOG_FILE"
cd "$ADMIN_DIR"

sudo chown -R $ADMIN_USER:$ADMIN_GROUP .
sudo find . -type d -exec chmod 775 {} \;
sudo find . -type f -exec chmod 664 {} \;

# Ajustar permissões executáveis para scripts críticos (se existirem)
[ -f artisan ] && sudo chmod +x artisan
[ -f deploy.sh ] && sudo chmod +x deploy.sh

# Ajustar permissões do .git (se existir)
if [ -d ".git" ]; then
    sudo chown -R $ADMIN_USER:$ADMIN_GROUP .git
    sudo find .git -type d -exec chmod 775 {} \;
    sudo find .git -type f -exec chmod 664 {} \;
fi

# Ajustar permissões específicas do diretório app/Console/Commands
if [ -d "app/Console/Commands" ]; then
    sudo chown -R $ADMIN_USER:$ADMIN_GROUP app/Console/Commands
    sudo find app/Console/Commands -type d -exec chmod 775 {} \;
    sudo find app/Console/Commands -type f -exec chmod 664 {} \;
fi

# 4.2. Atualizar código via Git
echo "==> Atualizando código via Git..." | tee -a "$LOG_FILE"
sudo -H -u $ADMIN_USER bash -c "git reset --hard origin/main"
sudo -H -u $ADMIN_USER bash -c "git pull"

# 5. Configurar storage
echo "==> Configurando storage..." | tee -a "$LOG_FILE"
rm -rf "$ADMIN_DIR/storage"
ln -sf "$EFS_MOUNTPOINT/storage" "$ADMIN_DIR/storage"
set_permissions "$EFS_MOUNTPOINT/storage"
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
    chown $ADMIN_USER:$ADMIN_GROUP "$ADMIN_DIR/.env"
    chmod 664 "$ADMIN_DIR/.env"
fi

# 7. Ajustar permissões críticas
echo "==> Ajustando permissões críticas..." | tee -a "$LOG_FILE"
set_permissions "$ADMIN_DIR/bootstrap/cache"
set_permissions "$EFS_MOUNTPOINT/storage"

# 8. Reiniciar serviços
echo "==> Reiniciando serviços..." | tee -a "$LOG_FILE"
sudo systemctl start php8.2-fpm
sleep 2
sudo systemctl start nginx
sleep 2

# 9. Verificar serviços
verify_services || {
    echo "ERRO: Falha na verificação dos serviços. Verifique os logs." | tee -a "$LOG_FILE"
    exit 1
}

# 11. Verificar montagem automática
if ! grep -q "$EFS_SERVER" /etc/fstab; then
    echo "$FSTAB_ENTRY" >> /etc/fstab
    echo "Entrada adicionada ao fstab." | tee -a "$LOG_FILE"
fi

# 12. Criar arquivos PHP utilitários
create_php_utilities

# 12.1. Limpar arquivos PHP temporários antigos
clean_temporary_files
php -r "opcache_reset(); echo 'OPcache reset successfully.';" | tee -a "$LOG_FILE"

# 13. Verificações finais
echo "==> Verificações finais..." | tee -a "$LOG_FILE"
verify_storage_link
verify_services

echo "==> Status final dos diretórios:" | tee -a "$LOG_FILE"
ls -la "$ADMIN_DIR" >> "$LOG_FILE"
ls -la "$EFS_MOUNTPOINT/storage" >> "$LOG_FILE"

echo "==> Deploy concluído com sucesso em $(date)" | tee -a "$LOG_FILE"


# 14. (Opcional) Configurar Git globalmente para o usuário ubuntu
#Caso deseje armazenar configs globais no /home/ubuntu em vez de /root, descomente as linhas abaixo:
echo "==> Configurando Git globalmente para o usuário $ADMIN_USER (opcional)..." | tee -a "$LOG_FILE"
sudo -u ubuntu -H git config --global core.attributesfile ~/.gitattributes
sudo -u ubuntu -H git config --global core.excludesfile ~/.gitignore

#15. Aquele refresh final maroto >:)
# Reiniciar o PHP-FPM
echo "==> Reiniciando serviços..." | tee -a "$LOG_FILE"
sudo systemctl restart php8.2-fpm
# Reiniciar o Nginx
sudo systemctl restart nginx

#16. Atualizar cache do Laravel
refresh_laravel_cache

#17. subir artisan
php artisan up

#18. Publicar Horizon
echo "==> Publicando Horizon..." | tee -a "$LOG_FILE"
sudo -H -u $ADMIN_USER bash -c "cd $ADMIN_DIR && php artisan horizon:publish"

#19. Se chegamos até aqui esse script é do bom, então vamos guardar no storage para quando as instâncias subirem novamente utilizarem ele
echo "==> Atualizando script de deploy no EFS..." | tee -a "$LOG_FILE"
rm -f "$EFS_MOUNTPOINT/deploy.sh"
cp "$ADMIN_DIR/deploy.sh" "$EFS_MOUNTPOINT/deploy.sh"

echo "Script finalizado com sucesso em: $(date)" | tee -a "$LOG_FILE"