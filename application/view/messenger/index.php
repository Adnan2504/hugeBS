<div class="container">
    <h1 class="title">Messenger</h1>
    <div class="box">

        <?php $this->renderFeedbackMessages(); ?>

        <div class="messenger-wrapper">
            <div class="user-list">
                <div class="user-list-header">
                    <h3>Users and Groups</h3>
                    <button class="user-add-button" >+</button>
                </div>

                <ul>
                    <?php foreach ($this->users as $user): ?>
                        <li>
                            <a href="<?php echo Config::get('URL') . 'messenger/index/' . $user->user_id . '?type=user'; ?>"
                               class="<?= ($user->user_id == $this->currentReceiverId && $this->currentType == 'user') ? 'active' : ''; ?>">
                                <?= htmlentities($user->user_name); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>

                    <?php foreach ($this->groups as $group): ?>
                        <li>
                            <a href="<?php echo Config::get('URL') . 'messenger/index/' . $group['id'] . '?type=group'; ?>"
                               class="<?= ($group['id'] == $this->currentGroupId && $this->currentType == 'group') ? 'active' : ''; ?>">
                                <?= htmlentities($group['name']); ?>
                                <small>[<?= htmlentities($group['members']); ?>]</small>
                            </a>
                        </li>
                    <?php endforeach; ?>

                </ul>

            </div>

            <div id="group-modal" class="modal" style="display: none;">
                <div class="modal-content">
                    <h2>Create Group</h2>
                    <form id="group-form" method="post" action="<?php echo Config::get('URL'); ?>groups/createGroup">
                        <label for="group-name">Group Name:</label>
                        <input type="text" id="group-name" name="group_name" required />

                        <label for="group-users">Select Users:</label>
                        <ul id="group-users">
                            <?php foreach ($this->users as $user): ?>
                                <li>
                                    <input type="checkbox" id="user-<?= $user->user_id; ?>" name="user_ids[]" value="<?= $user->user_id; ?>" />
                                    <label for="user-<?= $user->user_id; ?>"><?= htmlentities($user->user_name); ?></label>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <button type="submit">Create Group</button>
                        <button type="button" id="close-modal">Cancel</button>
                    </form>
                </div>
            </div>


            <div class="screen">
                <div class="conversation">
                    <?php if (!empty($this->messages)): ?>
                        <?php foreach ($this->messages as $message): ?>
                            <?php if ($this->currentType === 'group'): ?>
                                <?php if ($message['group_id'] == $this->currentGroupId): ?>
                                <!-- Group Messages -->
                                    <?php if ($message['sender_id'] == Session::get('user_id')): ?>
                                        <div class="messages messages--sent">
                                            <div class="message-status">
                                                <?= $message['is_read'] ? '<small>Seen</small>' : '<small>Unread</small>'; ?>
                                            </div>
                                            <div class="message"><?= htmlentities($message['message'] ?? ''); ?></div>
                                        </div>
                                    <?php else: ?>
                                        <div class="messages messages--received">
                                            <div class="message"><?= htmlentities($message['message'] ?? ''); ?></div>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php elseif ($this->currentType === 'user' && $message['receiver_id'] == $this->currentReceiverId): ?>
                                <!-- User Messages -->
                                <?php if ($message['sender_id'] == Session::get('user_id')): ?>
                                    <div class="messages messages--sent">
                                        <div class="message-status">
                                            <?= $message['is_read'] ? '<small>Seen</small>' : '<small>Unread</small>'; ?>
                                        </div>
                                        <div class="message"><?= htmlentities($message['message'] ?? ''); ?></div>
                                    </div>
                                <?php else: ?>
                                    <div class="messages messages--received">
                                        <div class="message"><?= htmlentities($message['message'] ?? ''); ?></div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No messages to display.</p>
                    <?php endif; ?>

                </div>


                <div class="text-bar">
                    <form class="text-bar__field" method="post" action="<?php echo Config::get('URL'); ?>messenger/create">
                        <input type="hidden" name="type" value="<?= !empty($this->currentGroupId) ? 'group' : 'user'; ?>" />
                        <input type="hidden" name="receiver_id" value="<?= htmlentities($this->currentReceiverId); ?>" />
                        <input type="hidden" name="group_id" value="<?= htmlentities($this->currentGroupId); ?>" />
                        <input type="text" name="message_body" placeholder="Type your message..." required />
                        <button type="submit" class="text-bar__thumb">
                            <span>Send</span>
                        </button>
                    </form>
                </div>

            </div>

            <script>
                document.querySelector('.user-add-button').addEventListener('click', () => {
                    document.getElementById('group-modal').style.display = 'block';
                });

                document.getElementById('close-modal').addEventListener('click', () => {
                    document.getElementById('group-modal').style.display = 'none';
                });
            </script>
        </div>
    </div>
</div>

