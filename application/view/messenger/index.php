<div class="container">
    <h1 class="title">Messenger</h1>
    <div class="box">

        <?php $this->renderFeedbackMessages(); ?>

        <div class="messenger-wrapper">
            <div class="user-list">
                <h3>Users</h3>
                <ul>
                    <?php foreach ($this->users as $user): ?>
                        <li>
                            <a href="<?php echo Config::get('URL') . 'messenger/index/' . $user->user_id; ?>"
                               class="<?= ($user->user_id == $this->currentReceiverId) ? 'active' : ''; ?>">
                                <?= htmlentities($user->user_name); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="screen">
                <div class="conversation">
                    <?php if (!empty($this->messages)): ?>
                        <?php foreach ($this->messages as $message): ?>
                            <?php if ($message->sender_id == Session::get('user_id')): ?>
                                <div class="messages messages--sent">
                                    <div class="message"><?= htmlentities($message->message); ?></div>
                                </div>
                            <?php else: ?>
                                <div class="messages messages--received">
                                    <div class="message"><?= htmlentities($message->message); ?></div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No messages yet. Start a conversation!</p>
                    <?php endif; ?>
                </div>

                <div class="text-bar">
                    <form class="text-bar__field" method="post" action="<?php echo Config::get('URL'); ?>messenger/create">
                        <input type="hidden" name="receiver_id" value="<?= htmlentities($this->currentReceiverId); ?>" />
                        <input type="text" name="message_body" placeholder="Type your message..." required />
                        <button type="submit" class="text-bar__thumb">
                            <span>Send</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
