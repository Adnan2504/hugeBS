<?php

class MessengerModel
{
    /**
     * Get all messages for the logged-in user.
     * @param int $user_id The ID of the logged-in user.
     * @return array An array of messages.
     */
    public static function getAllMessages($user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT m.id, m.sender_id, m.receiver_id, m.message, m.is_read, m.created_at, u.user_name AS sender_name
            FROM messages m
            JOIN users u ON m.sender_id = u.user_id
            WHERE m.receiver_id = :user_id
            ORDER BY m.created_at DESC";

        $query = $database->prepare($sql);
        $query->execute([':user_id' => $user_id]);

        return $query->fetchAll();
    }

    /**
     * Get a list of users who have messaged the logged-in user.
     * @param int $user_id The ID of the logged-in user.
     * @return array A list of unique conversations.
     */
    public static function getConversations($user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT DISTINCT  u.user_id, u.user_name FROM messages m JOIN users u ON m.sender_id = u.user_id
            WHERE  m.receiver_id = :user_id OR  m.sender_id = :user_id ORDER BY m.created_at DESC";

        $query = $database->prepare($sql);
        $query->execute([':user_id' => $user_id]);

        return $query->fetchAll();
    }

    /**
     * Get the chat history between two users.
     * @param int $user_id The ID of the logged-in user.
     * @param int $other_user_id The ID of the other user.
     * @return array The chat history.
     */
    public static function getChatHistory($user_id, $other_user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT * FROM messages
            WHERE (sender_id = :user_id AND receiver_id = :other_user_id)
               OR (sender_id = :other_user_id AND receiver_id = :user_id)
            ORDER BY created_at ASC";

        $query = $database->prepare($sql);
        $query->execute([
            ':user_id' => $user_id,
            ':other_user_id' => $other_user_id,
        ]);

        return $query->fetchAll();
    }


    /**
     * Send a new message.
     * @param int $sender_id The ID of the sender.
     * @param int $receiver_id The ID of the recipient.
     * @param string $message The content of the message.
     * @return bool True if the message was sent, false otherwise.
     */
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


    /**
     * Delete a message.
     * @param int $message_id The ID of the message to delete.
     * @param int $user_id The ID of the logged-in user.
     * @return bool True if the message was deleted, false otherwise.
     */
    public static function deleteMessage($message_id, $user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "DELETE FROM messages WHERE id = :message_id AND (sender_id = :user_id OR receiver_id = :user_id)";
        $query = $database->prepare($sql);
        $query->execute([
            ':message_id' => $message_id,
            ':user_id' => $user_id,
        ]);

        return $query->rowCount() > 0;
    }
}
