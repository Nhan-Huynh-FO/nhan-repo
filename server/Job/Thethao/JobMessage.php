<?php

/**
 * @author      :   hungnt1@fpt.net
 * @name        :   JobMessage
 * @version     :   300312
 * @copyright   :   My company
 */
class Job_Thethao_JobMessage
{

    /**
     * Send mail function
     * @param <array> $params
     * @return <array>
     * @author HungNT1
     */
    public function sendMail($params)
    {
        // Default return
        $response = true;


        // Include config send mail
        include(APPLICATION_PATH . '/configs/mail.php');

        if (is_array($configsender) && !empty($configsender))
        {
            // Create transport
            $transport = new Zend_Mail_Transport_Smtp($smtpServer['default'], $configmail);
            $transportLB = new Zend_Mail_Transport_Smtp($smtpServer['balancing'], $configmail);

            foreach ($params['reciever'] as $receiver)
            {
                $email = $receiver['email'];
                $alias = $receiver['alias'];
                //init zend mail
                $mail = new Zend_Mail('UTF-8');

                //set from
                $mail->setFrom($configsender['message']['email'], $params['name']);

                //receivers
                $mail->addTo($email, $alias);

                //set content mail
                $mail->setSubject($params['subject']);

                $mail->setBodyHtml($params['body']);

                try
                {
                    //send mail
                    if (!$mail->send($transport))
                    {
                        $response = false;
                    }
                }
                catch (Exception $ex)
                {
                    // Log error
                    Thethao_Global::sendLog($ex);
                    try
                    {
                        //send mail
                        if (!$mail->send($transportLB))
                        {
                            $response = false;
                        }
                    }
                    catch (Exception $ex1)
                    {
                        // Log error
                        Thethao_Global::sendLog($ex1);
                    }
                }
            }
        }
        else
        {
            $response = false;
        }

        return $response;
    }

}
