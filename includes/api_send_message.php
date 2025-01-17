<?php

    global $DATA_OBJ, $DB, $info;

    $logged_user = $_SESSION['userid'];

    if (isset($DATA_OBJ->group_id)) { // if it is a group message

        // get group

        // send message
        $arr = [];
        $arr['group_id'] = $DATA_OBJ->group_id;
        $arr['sender'] = $logged_user;
        $arr['message'] = $DATA_OBJ->text;
        $arr['date'] = date('Y-m-d H:i:s');
        $sql_send = "INSERT INTO `messages` (sender, txt_message, date, group_id) VALUES 
                        (:sender, :message, :date, :group_id)";
        $result = $DB->write($sql_send, $arr);

        if ($result) {
            $info->message = "Group message successfully sent";
            $info->group_id = $DATA_OBJ->group_id;
            $info->data_type = "send_message"; // send to responseText
        } else {
            $info->chat_contact = "Group message not sent due to error";
            $info->data_type = "error";
        }


    } else {

        $arr['receiver_userid'] = "";

        if (isset($DATA_OBJ->receiver_userid)) {

//        checking if chat already exist
            $result_chat = $DB->chatFinder($DATA_OBJ->receiver_userid, $_SESSION['userid']);

//      send the message to db

            $arr2 = [];
            if ($result_chat) { // if chat exist
                // send this message to the chat
                $chat = $result_chat;
                $arr2['chat_id'] =$chat->chat_id;
            } else {
                $arr2['chat_id'] = generateRandomString(10);
            }

            $arr2['receiver_userid'] = $DATA_OBJ->receiver_userid;
            $arr2['sender_userid'] = $_SESSION['userid']; // my user id
            $arr2['message'] = $DATA_OBJ->text;
            $arr2['date'] = date('Y-m-d H:i:s');
            $sql_send = "INSERT INTO `messages` (chat_id, sender, receiver, txt_message, date) VALUES 
                        (:chat_id, :sender_userid, :receiver_userid, :message, :date)";
            $result = $DB->write($sql_send, $arr2);

            if ($result) {
                $info->message = "Message successfully sent";
                $info->receiver_id = $arr2['receiver_userid'];
                $info->data_type = "send_message"; // send to responseText
            } else {
                $info->chat_contact = "Message not sent due to error";
                $info->data_type = "error";
            }
        } else {
            $info->message = "Select a contact first";
            $info->data_type = "error";
        }

    }



    echo json_encode($info);









