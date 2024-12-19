<?php

class MessengerModel
{
    public static function getChatHistory($user_id, $other_user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT * FROM messages
            WHERE (sender_id = :user_id AND receiver_id = :other_user_id)
               OR (sender_id = :other_user_id AND receiver_id = :user_id)
            ORDER BY created_at ASC";

        $updateSql = "UPDATE messages
                  SET is_read = 1
                  WHERE receiver_id = :user_id AND sender_id = :other_user_id AND is_read = 0";

        $updateQuery = $database->prepare($updateSql);
        $updateQuery->execute([
            ':user_id' => $user_id,
            ':other_user_id' => $other_user_id,
        ]);

        $query = $database->prepare($sql);
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

        $sql = "INSERT INTO messages (sender_id, receiver_id, message, is_read, created_at)
            VALUES (:sender_id, :receiver_id, :message, 0, NOW())";

        $query = $database->prepare($sql);

        return $query->execute([
            ':sender_id' => $sender_id,
            ':receiver_id' => $receiver_id,
            ':message' => $message,
        ]);
    }


    public static function getGroupChatHistory($group_id, $user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        // Check if the user is a member of the group
        $checkMembershipSql = "SELECT COUNT(*) FROM group_members WHERE group_id = :group_id AND user_id = :user_id";
        $membershipQuery = $database->prepare($checkMembershipSql);
        $membershipQuery->execute([
            ':group_id' => $group_id,
            ':user_id' => $user_id,
        ]);

        $isMember = $membershipQuery->fetchColumn();

        if (!$isMember) {
            return [];
        }

        // Fetch the group chat history if the user is a member
        $sql = "SELECT m.id, m.sender_id, m.group_id, m.message, m.is_read, m.created_at, u.user_name AS sender_name
            FROM messages m
            JOIN users u ON m.sender_id = u.user_id
            WHERE m.group_id = :group_id
            ORDER BY m.created_at ASC";

        $query = $database->prepare($sql);
        $query->execute([':group_id' => $group_id]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
