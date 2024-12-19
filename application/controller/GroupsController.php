<?php

class GroupsController extends Controller
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
            $messages = MessengerModel::getGroupChatHistory($currentGroupId, $user_id);
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
     * Create a new group and add the creator as a member.
     */
    public function createGroup()
    {
        $groupName = Request::post('group_name');
        $userIds = Request::post('user_ids', []);

        if (empty($groupName)) {
            Session::add('feedback_negative', 'Group name cannot be empty.');
            Redirect::to('messenger/index');
            return;
        }

        $creatorId = Session::get('user_id');
        $userIds[] = $creatorId;
        $userIds = array_unique($userIds);

        $groupId = GroupModel::createGroup($groupName, $creatorId);

        if ($groupId) {
            foreach ($userIds as $userId) {
                GroupModel::addUserToGroup($groupId, $userId);
            }
            Session::add('feedback_positive', 'Group created successfully!');
        } else {
            Session::add('feedback_negative', 'Failed to create group.');
        }

        Redirect::to('messenger/index');
    }
}
