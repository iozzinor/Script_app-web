
<div id="main_toolbar">
    <div id="sct_language_container">
        <label id="sct_language_label" for="sct_language"></label>
        <select id="sct_language" name="sct_language"></select>
    </div>
    <p id="sct_subject_status"></p>
    <div id="main_toolbar_buttons">
    </div>
</div>

<form id="sct_form" action="new_sct_subject/send" method="POST">
    <div id="sct_questions">
    </div>
</form>

<section id="errors_section">
    <h1 id="errors_title"><?= _d('new_sct_subject', 'errors_title'); ?></h1>
    <p id='errors_count'></p>
    <p id="errors_message"><?= _d('new_sct_subject', 'Please fix the following points:'); ?></p>
    <ul id="errors_list">
    </ul>
</section>

<?= $sct_information_script ?>