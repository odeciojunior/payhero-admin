<?php

use Illuminate\Database\Migrations\Migration;
use Modules\Core\Entities\ProjectNotification;
use Modules\Core\Services\ProjectNotificationService;

class UpdateProjectNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $projectsNotification = ProjectNotification::groupBy("project_id")->get("project_id");

        foreach ($projectsNotification as $projectNotrification) {
            ProjectNotification::firstOrCreate([
                "type_enum" => ProjectNotificationService::EMAIL_TYPE,
                "event_enum" => ProjectNotificationService::PIX_GENERATED,
                "time" => "Imediato",
                "message" => json_encode([
                    "subject" => "Seu código pix foi gerado",
                    "title" => "Efetue o pagamento, a promoção termina hoje!",
                    "content" => "Olá {primeiro_nome}, não esqueça de pagar seu PIX para enviarmos seu pedido!",
                ]),
                "notification_enum" => 16,
                "project_id" => $projectNotrification->project_id,
            ]);

            ProjectNotification::firstOrCreate([
                "type_enum" => ProjectNotificationService::EMAIL_TYPE,
                "event_enum" => ProjectNotificationService::PIX_COMPENSATED,
                "time" => "Imediato",
                "message" => json_encode([
                    "subject" => "PIX pago - Pedido {codigo_venda} ",
                    "title" => "PIX pago com sucesso!",
                    "content" =>
                        "Olá {primeiro_nome}, seu pedido {codigo_venda} foi aprovado. Obrigado pela sua compra, nos próximos dias enviaremos o código de rastreio para você acompanhar seu pedido.",
                ]),
                "notification_enum" => 17,
                "project_id" => $projectNotrification->project_id,
            ]);

            ProjectNotification::firstOrCreate([
                "type_enum" => ProjectNotificationService::EMAIL_TYPE,
                "event_enum" => ProjectNotificationService::PIX_EXPIRED,
                "time" => "Imediato",
                "message" => json_encode([
                    "subject" => "Finalize sua compra no PIX",
                    "title" => "Seu PIX expirou!",
                    "content" =>
                        "Olá {primeiro_nome}, seu pagemento por PIX expirou, mas não se preocupe, você pode regerar o PIX de pagamento clicando no botão abaixo: ",
                ]),
                "notification_enum" => 18,
                "project_id" => $projectNotrification->project_id,
            ]);
        }

        $projectsStatusFalse = ProjectNotification::where("status", false)
            ->whereIn("notification_enum", [11, 12, 13])
            ->get();

        foreach ($projectsStatusFalse as $projectStatusFalse) {
            $projectStatusFalse->update(["status" => true]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $projectsNotification = ProjectNotification::whereIn("notification_enum", [
            ProjectNotificationService::PIX_GENERATED,
            ProjectNotificationService::PIX_COMPENSATED,
            ProjectNotificationService::PIX_EXPIRED,
        ])->get();

        foreach ($projectsNotification as $projectNotrification) {
            $projectNotrification->forceDelete();
        }
    }
}
