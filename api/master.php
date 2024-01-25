<?php
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type,Access-Control-Allow-Methods,Access-Control-Allow-Headers,Authorization, X-Requested-With');

    include('db.php');

    $data = json_decode(file_get_contents("php://input"),true);

    $mode = $data['mode'];

    if($mode == 'contactFromWebsite')
    {
        $name = trim(str_replace("'","",$data['name']));
        $email = trim(str_replace("'","",$data['email']));
        $subject = trim(str_replace("'","",$data['subject']));
        $message = trim(str_replace("'","",$data['message']));

        // get ip address 
            $IP_Address=getenv('REMOTE_ADDR');
                        
        $query="INSERT INTO t_contact
                (
                    `name`,
                    `email`,
                    `subject`,
                    `message`,
                    `ip_address`
                )
                VALUES
                (
                    '$name',
                    '$email',
                    '$subject',
                    '$message',
                    '$IP_Address'
                )";

        if( mysqli_query($conn,$query))
        {
            echo json_encode(array('message'=>'Your message has been sent. Thank you!','status'=>'success'));
            //  send response copy to the user 
            $send_response=file_get_contents('sendresponse.html');	

            $find="{contact_person_name}";
            $send_response=str_replace($find,$name,$send_response);
    
            $to_admin = $email;
        
            $from_email="noreply@mritunjay.com";
    
            $res_subject = "Thank You for Reaching Out - Let's Connect!";
    
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $headers .= 'From:Mritunjay Pramanick <'.$from_email.'>' . "\r\n";
            $headers .= "X-Mailer: PHP".phpversion();
            mail($to_admin, $res_subject, $send_response, $headers);    


            //  send response copy to the person who own the portfolio 
            $send_notification=file_get_contents('notification.html');	

            $find="{user_name}";
            $send_notification=str_replace($find,$name,$send_notification);

            $find="{user_email}";
            $send_notification=str_replace($find,$email,$send_notification);

            $find="{user_subject}";
            $send_notification=str_replace($find,$subject,$send_notification);

            $find="{user_message}";
            $send_notification=str_replace($find,$message,$send_notification);

            $to_admin = 'mritunjay.softech@gmail.com';
        
            $from_email="noreply@mritunjay.com";
    
            $notification_subject = "New Contact Form Submission on Your Portfolio.";
    
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $headers .= 'From:Mritunjay Pramanick <'.$from_email.'>' . "\r\n";
            $headers .= "X-Mailer: PHP".phpversion();
            mail($to_admin, $notification_subject, $send_notification, $headers);    
        }
        else
        {
            echo json_encode(array('message'=>'Failed to sent message. Sorry !','status'=>'failed'));
        }
    }

?>