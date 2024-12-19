<?php

class MessengerController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::checkAuthentication();
    }

    /**
     * Display all messages for the logged-in user.
     */
    public function index($id = null)
    {
        $type = $_GET['type'] ?? 'user';
        $user_id = Session::get('user_id');
        $users = UserModel::getPublicProfilesOfAllUsers();
        $groups = GroupModel::getAllGroups($user_id);

        $messages = [];
        if ($type === 'user' && $id) {
            $currentReceiverId = $id;
            $currentGroupId = null;
            $messages = MessengerModel::getChatHistory($user_id, $currentReceiverId);
        } elseif ($type === 'group' && $id) {
            $currentReceiverId = null;
            $currentGroupId = $id;
            $messages = GroupModel::getGroupChatHistory($currentGroupId, $user_id);
        } else {
            $currentReceiverId = null;
            $currentGroupId = null;
        }

        $this->View->render('messenger/index', [
            'users' => $users,
            'groups' => $groups,
            'messages' => $messages,
            'currentReceiverId' => $currentReceiverId,
            'currentGroupId' => $currentGroupId,
            'currentType' => $type,
        ]);
    }

    /**
     * Create a new message.
     */
    public function create()
    {
        $type = Request::post('type');
        $receiver_id = Request::post('receiver_id');
        $group_id = Request::post('group_id');
        $message_body = Request::post('message_body');

        if (empty($message_body)) {
            Session::add('feedback_negative', 'Message cannot be empty.');
            Redirect::to('messenger');
            return;
        }
        $success = false;

        if ($type === 'group' && $group_id) {
            $success = GroupModel::sendGroupMessage(Session::get('user_id'), $group_id, $message_body);
        } elseif ($type === 'user' && $receiver_id) {
            $success = MessengerModel::sendMessage(Session::get('user_id'), $receiver_id, $message_body);
        }

        if ($success) {
            Session::add('feedback_positive', 'Message sent successfully.');
        } else {
            Session::add('feedback_negative', 'Failed to send message.');
        }

        Redirect::to('messenger');
    }


    public static function createGroup($name, $creatorId)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "INSERT INTO groups (name, created_by) VALUES (:name, :creator_id)";
        $query = $database->prepare($sql);
        $query->execute([':name' => $name, ':creator_id' => $creatorId]);

        return $database->lastInsertId();
    }


    /**
     * Delete a message.
     * @param int $message_id ID of the message to delete.
     */
    public function delete($message_id)
    {
        if (MessengerModel::deleteMessage($message_id, Session::get('user_id'))) {
            Session::add('feedback_positive', 'Message deleted.');
        } else {
            Session::add('feedback_negative', 'Failed to delete message.');
        }

        Redirect::to('messenger');
    }
}
