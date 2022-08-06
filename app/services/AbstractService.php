<?php

namespace App\Services;

/**
 * Class AbstractService
 *
 * @property \Phalcon\Db\Adapter\Pdo\Postgresql $db
 * @property \Phalcon\Config $config
 */

use App\Controllers\WalletController;
use App\Models\ComissionRate;
use App\Models\ComissionRateWithdrawalPoint;
use App\Models\Guarantee;
use App\Models\Product;
use App\Models\User;
use PHPMailer\PHPMailer\PHPMailer;
use App\Controllers\NotificationController;
use App\Models\InsurancePolicyEndorsement;
use App\Models\Prime;
use App\Models\SubGuarantee;
use PHPMailer\PHPMailer\Exception;
use Nuzkito\ChromePdf\ChromePdf;

abstract class AbstractService extends \Phalcon\DI\Injectable
{

    /**
     * Invalid parameters anywhere
     */
    const ERROR_INVALID_PARAMETERS = 10001;

    /**
     * Record already exists
     */
    const ERROR_ALREADY_EXISTS = 10002;

    /**
     * Nombre minimum de code promo par offre
     */
    const CODE_PROMO_SEUIL = 2;

    /**
     * Les profils qui peuvent avoir un wallet
     */

    /**
     *
     * @param type $destinaire
     * @param type $subject
     * @param type $body
     * @return type
     */
    public static function mail($destinaire, $subject, $body)
    {
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';
        $mail->Host = 'server1.bloom.bj';
        $mail->Port = 465;
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->Username = "noreply@bloom.bj";
        $mail->Password = "lpR~nBu1y%1E";
        $mail->setFrom('noreply@bloom.bj', 'BLOOM INSURANCE');
        $mail->addAddress($destinaire, 'Client');
        $mail->addBCC('emaileur.automatique@gmail.com', 'Auto Copie');
        $mail->isHTML(true);
        $mail->Subject = htmlspecialchars_decode($subject);
        $mail->Body = $body;
        $result = array("status" => "success", "message" => "Mail envoyé");
        if (!$mail->send()) {
            $result = array("status" => "error", "message" => "Une erreur est survenue lors l'envoi du mail.");
        }
        return json_encode($result);
    }

//    public static function uploadFile($request, $fileKey, $allowExtensions, $longPath, $shortPath)
//    {
//        $output["success"] = FALSE;
//        $output["error"] = TRUE;
//        $output["message"] = 'Aucun fichier trouvé, veuillez telecharger une image';
//        if ($request->hasFiles()) {
//            $files = $request->getUploadedFiles();
//
//            // Print the real file names and sizes  && $file->getKey()==$fileKey
//            foreach ($files as $file) {
//
//                if ($file->getKey() == $fileKey) {
//                    if (!$file->isUploadedFile()) {
//                        $output["error"] = TRUE;
//                        $output["message"] = "Le fichier est introuvable";
//                        return $output;
//                    }
////		    $temp_name	 = $file->getTempName();
//                    $type = $file->getExtension();
//                    $chaine = md5(uniqid(rand(), true));
//                    $name_file = "{" . $chaine . "}" . "." . "{$type}";
//                    if (!in_array($type, $allowExtensions)) {
//                        $output["error"] = TRUE;
//                        $output["message"] = "Le fichier n'est pas au bon format. Les format autorisés sont: ";
//                        foreach ($allowExtensions as $value) {
//                            $output["message"] .= $value . ' ';
//                        }
//
//                        return $output;
//                    }
//
//                    ($file->moveTo($longPath . $name_file)) ? $isUploaded = true : $isUploaded = false;
//
//                    if (!$isUploaded) {
//                        $output["error"] = TRUE;
//                        $output["message"] = "Echec de telechargement de l'image veuillez ressayer";
//                        return $output;
//                    }
//
//                    $output["success"] = TRUE;
//                    $output["error"] = FALSE;
//                    $output["filePath"] = $shortPath . $name_file;
//                    return $output;
////		    elseif ( ! move_uploaded_file( $temp_name, $this->profil . $name_file ) ) {
////			$this->flashSession->error( "Impossible de copier le fichier dans $content_dir" );
////			return $this->response->redirect( $this->url->getBaseUri() . "compte/profil", true );
////		    } else
////			$img = "/public/profil/" . $name_file;
//                }
//            }
//        }
//        return $output;
//    }

    public static function send_mail_and_notification($user_id, $objet, $data, $email)
    {
        $template_mail_data=$data;
        //send mail to user
        ob_start();
        require_once __DIR__ . '/../mailstemplate/template_mail.php';
        $message = ob_get_clean();
        //send notification to user
        $sendeur_tab = ["user_id" => $user_id,
            "message" => $message];
        $json_data = json_encode($sendeur_tab);
        $notif_controller = new NotificationController();
        $notif = $notif_controller->add_action($json_data);
        AbstractService::mail($email, $objet, $message);
    }



}
