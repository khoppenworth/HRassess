<?php
require_once __DIR__.'/../config.php';
auth_required(['admin']);
echo "<h1>Admin Dashboard</h1>";
echo "<ul>
<li><a href='users.php'>Manage Users</a></li>
<li><a href='questionnaire_manage.php'>Manage Questionnaires</a></li>
<li><a href='supervisor_review.php'>Supervisor Reviews</a></li>
<li><a href='analytics.php'>Analytics</a></li>
<li><a href='export.php'>Export CSV</a></li>
</ul>";
?>