<?php

class GroupModel
{
    public static function createGroup($name, $creatorId)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "INSERT INTO groups (name, created_by) VALUES (:name, :creator_id)";
        $query = $database->prepare($sql);
        $query->execute([':name' => $name, ':creator_id' => $creatorId]);

        return $database->lastInsertId();
    }

    public static function addUserToGroup($groupId, $userId)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "INSERT INTO group_members (group_id, user_id) VALUES (:group_id, :user_id)";
        $query = $database->prepare($sql);
        return $query->execute([':group_id' => $groupId, ':user_id' => $userId]);
    }

    public static function getAllGroups($userId)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT groups.id, groups.name, GROUP_CONCAT(users.user_name SEPARATOR ', ') AS members
            FROM groups
            LEFT JOIN group_members ON groups.id = group_members.group_id
            LEFT JOIN users ON group_members.user_id = users.user_id
            WHERE groups.id IN (
                SELECT group_id FROM group_members WHERE user_id = :user_id
            )
            GROUP BY groups.id";

        $query = $database->prepare($sql);
        $query->execute([':user_id' => $userId]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function sendGroupMessage($sender_id, $group_id, $message)
    {
        if (empty($message)) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "INSERT INTO messages (sender_id, group_id, message, is_read, created_at)
            VALUES (:sender_id, :group_id, :message, 0, NOW())";

        $query = $database->prepare($sql);

        return $query->execute([
            ':sender_id' => $sender_id,
            ':group_id' => $group_id,
            ':message' => $message,
        ]);
    }


    public static function getGroupChatHistory($groupId, $userId)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        // Verify if the user is a member of the group
        $checkMembershipSql = "SELECT COUNT(*) FROM group_members WHERE group_id = :group_id AND user_id = :user_id";
        $membershipQuery = $database->prepare($checkMembershipSql);
        $membershipQuery->execute([
            ':group_id' => $groupId,
            ':user_id' => $userId,
        ]);

        $isMember = $membershipQuery->fetchColumn();

        if (!$isMember) {
            // User is not a member of the group
            return [];
        }

        // Fetch the group chat history if the user is a member
        $sql = "SELECT m.id, m.sender_id, m.group_id, m.message, m.is_read, m.created_at, u.user_name AS sender_name
            FROM messages m
            JOIN users u ON m.sender_id = u.user_id
            WHERE m.group_id = :group_id
            ORDER BY m.created_at ASC";

        $query = $database->prepare($sql);
        $query->execute([':group_id' => $groupId]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

}
