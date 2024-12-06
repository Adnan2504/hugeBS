<?php

/**
 * Class UserRoleModel
 *
 * This class contains everything that is related to up- and downgrading accounts.
 */
class UserRoleModel
{
    /**
     * Upgrades / downgrades the user's account. Currently it's just the field user_account_type in the database that
     * can be 1 or 2 (maybe "basic" or "premium"). Put some more complex stuff in here, maybe a pay-process or whatever
     * you like.
     *
     * @param $type
     *
     * @return bool
     */
    public static function changeUserRole($type)
    {
        if (!$type) {
            return false;
        }

        // save new role to database
        if (self::saveRoleToDatabase($type)) {
            Session::add('feedback_positive', Text::get('FEEDBACK_ACCOUNT_TYPE_CHANGE_SUCCESSFUL'));
            return true;
        } else {
            Session::add('feedback_negative', Text::get('FEEDBACK_ACCOUNT_TYPE_CHANGE_FAILED'));
            return false;
        }
    }

    /**
     * Fetches the current role of the user from the database.
     *
     * @param int $userId The ID of the user.
     *
     * @return int|null The current role of the user or null if not found.
     */
    public static function getUserRole($userId)
    {
        if (!$userId) {
            return null;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("SELECT user_account_type FROM users WHERE user_id = :user_id LIMIT 1");
        $query->execute([':user_id' => $userId]);

        $result = $query->fetch();

        return $result ? (int)$result->user_account_type : null;
    }

    /**
     * Writes the new account type marker to the database and to the session
     *
     * @param $type
     *
     * @return bool
     */
    public static function saveRoleToDatabase($type)
    {
        // if $type is not 1 or 2
        if (!in_array($type, [1, 2])) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("UPDATE users SET user_account_type = :new_type WHERE user_id = :user_id LIMIT 1");
        $query->execute(array(
            ':new_type' => $type,
            ':user_id' => Session::get('user_id')
        ));

        if ($query->rowCount() == 1) {
            // set account type in session
            Session::set('user_account_type', $type);
            return true;
        }

        return false;
    }
}
