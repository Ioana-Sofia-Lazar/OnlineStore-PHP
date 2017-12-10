    <ul id="log">
        <li id="floatRight"><a href="?logout=true">Logout</a></li>
    </ul>

<?php
if (isset($_GET['logout'])) {
    session_destroy();
    echo "<script>window.open('index.php','_self')</script>";
}
?>