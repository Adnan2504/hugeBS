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
    public function index($receiver_id = null)
    {
        $user_id = Session::get('user_id');

        $users = UserModel::getPublicProfilesOfAllUsers();

        $currentReceiverId = $receiver_id ?: (isset($users[0]) ? $users[0]->user_id : null);

        $messages = [];
        if ($currentReceiverId) {
            $messages = MessengerModel::getChatHistory($user_id, $currentReceiverId);
        }

        $this->View->render('messenger/index', [
            'users' => $users,
            'messages' => $messages,
            'currentReceiverId' => $currentReceiverId,
        ]);
    }



    /**
     * Create a new message.
     */
    public function create()
    {
        $receiver_id = Request::post('receiver_id');
        $message_body = Request::post('message_body');

        if (MessengerModel::sendMessage(Session::get('user_id'), $receiver_id, $message_body)) {
            Session::add('feedback_positive', 'Message sent successfully.');
        } else {
            Session::add('feedback_negative', 'Failed to send message.');
        }

        Redirect::to('messenger');
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
