<?php

class MessengerModel
{
    public static function getChatHistory($user_id, $other_user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $updateQuery = $database->prepare("CALL MarkMessagesAsRead(:user_id, :other_user_id)");
        $updateQuery->execute([
            ':user_id' => $user_id,
            ':other_user_id' => $other_user_id,
        ]);

        $query = $database->prepare("CALL GetChatHistory(:user_id, :other_user_id)");
        $query->execute([
            ':user_id' => $user_id,
            ':other_user_id' => $other_user_id,
        ]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function sendMessage($sender_id, $receiver_id, $message)
    {
        if (empty($message)) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("CALL SendMessage(:sender_id, :receiver_id, :message)");
        return $query->execute([
            ':sender_id' => $sender_id,
            ':receiver_id' => $receiver_id,
            ':message' => $message,
        ]);
    }

    public static function getGroupChatHistory($group_id, $user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $membershipQuery = $database->prepare("CALL CheckGroupMembership(:group_id, :user_id)");
        $membershipQuery->execute([
            ':group_id' => $group_id,
            ':user_id' => $user_id,
        ]);

        $isMember = $membershipQuery->fetchColumn();

        if (!$isMember) {
            return [];
        }

        $markAsReadQuery = $database->prepare("CALL MarkGroupMessagesAsRead(:group_id, :user_id)");
        $markAsReadQuery->execute([
            ':group_id' => $group_id,
            ':user_id' => $user_id,
        ]);

        $query = $database->prepare("CALL GetGroupChatHistory(:group_id)");
        $query->execute([':group_id' => $group_id]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
