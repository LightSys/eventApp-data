<?php 
    $id = $_GET['id'];

    echo ( 
        '<nav>'
        . '<ol>'
        . '<a href="general.php?id=' . $id . '"><li id="general">General</li></a>'
        .    '<a href="contact-page.php?id=' . $id . '"><li id="contact-page-sections">Contact Page Sections</li></a>'
        .     '<a href="contacts.php?id=' . $id . '"><li id="contacts">Contacts</li></a>'
        .     '<a href="schedule.php?id=' . $id . '"><li id="schedule">Schedule</li></a>'
        .     '<a href="information-page.php?id=' . $id . '"><li id="information-pages">Information Pages</li></a>'
        .     '<a href="housing.php?id=' . $id . '"><li id="housing">Housing</li></a>'
        .     '<a href="prayer-partners.php?id=' . $id . '"><li id="prayer-partners">Prayer Partners</li></a>'
        .     '<a href="advanced.php?id=' . $id . '"><li id="advanced">Advanced</li></a>'
        . '</ol>'
        . '</nav>');
?>