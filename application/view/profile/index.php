<div class="container">
    <h1>ProfileController/index</h1>
    <div class="box">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <h3>What happens here ?</h3>
        <div>
            This controller/action/view shows a list of all users in the system. You could use the underlying code to
            build things that use profile information of one or multiple/all users.
        </div>
        <div>
            <table class="overview-table">
                <thead>
                <tr>
                    <td>Id</td>
                    <td>Avatar</td>
                    <td>Username</td>
                    <td>User's email</td>
                    <td>User Role</td>
                    <td>Link to user's profile</td>
                </tr>
                </thead>
                <?php foreach ($this->users as $user) { ?>
                    <tr class="<?= ($user->user_active == 0 ? 'inactive' : 'active'); ?>">
                        <td><?= $user->user_id; ?></td>
                        <td class="avatar">
                            <?php if (isset($user->user_avatar_link)) { ?>
                                <img src="<?= $user->user_avatar_link; ?>" />
                            <?php } ?>
                        </td>
                        <td><input type="text" id="userNameInput" name="userNameInput" value="<?= $user->user_name;?>"/>
                        </td>
                        <td><input type="text" id="userEmail" name="userEmail" value="<?= $user->user_email;?>"/>
                        <td><select size="1" id="userGroup" name="userGroup">
                                <option value="Guest" selected="selected">
                                    Guest
                                </option>
                                <option value="User">
                                    User
                                </option>
                                <option value="Admin">
                                    Admin
                                </option>
                            </select></td>
                        <td>
                            <a href="<?= Config::get('URL') . 'profile/showProfile/' . $user->user_id; ?>">Profile</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.0/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.0/css/jquery.dataTables.min.css" />

    <script>
        $(document).ready(function () {
            $('.overview-table').DataTable({
                columnDefs: [
                    {
                        orderable: false,
                        targets: [1, 2, 3]
                    }
                ]
            });
        });

    </script>

</div>
