<?php

/**
 * The Help Page HTML
 * 
 * @link       https://www.apdevops.com/
 * @since      1.0.0
 *
 * @package    Clickup_Task_Forms
 * @subpackage Clickup_Task_Forms/admin/partials
 */


 $help_images =  plugin_dir_url( __DIR__ ) . 'images/';

?>
<div id="integration-instructions" class="ctf-admin-container">
    <h2>API Integration</h2>
    <p>You will need to have Administrative access on your ClickUp account in order to create the necessary API credentials.</p>
    <h3>Getting your API Keys:</h3>
    <div class="row align-items-center">
        <div class="col-sm-1 mb-md-5 mb-3">
            <img src="<?php echo $help_images; ?>help-menu.png" class="ctf-help-img" />
        </div>
        <div class="col-md-2 mb-md-5 mb-3">
            <p>Once logged into ClickUp navigate to the bottom left menu and click the profile menu icon to open the settings menu.</p>
            <p>Then, click on "Integrations".</p>
        </div>
        <div class="col-md-4 mb-md-5 mb-3">
            <img src="<?php echo $help_images; ?>help-integrations.png" class="ctf-help-img" />
        </div>
        <div class="col-md-5 mb-md-5 mb-3">
            <p>From the Integrations Settings Page: Click on The ClickUp Cloud API Icon.</p>
            <img src="<?php echo $help_images; ?>help-cloud_api.png" class="ctf-help-img" /></p>
        </div>
        <div class="col-md-5 mb-md-5 mb-3">
            <p>Once the section loads, below the integrations selection panel, click on "+ Create an App"</p>
        </div>
        <div class="col-md-7 mb-md-5 mb-3">
            <img src="<?php echo $help_images; ?>help-create_app_1.png" class="ctf-help-img" />
        </div>
        <div class="col-md-2 mb-md-5 mb-3">
            <p>You will see the pop-up panel which will need to be propagated with an App Name and the Domain(s) that you wish to connect.</p>
            <p>Once you have entered this information, click "Create App". You will then see the Client ID and the Client Secret that you will need to copy into the API Integration settings page of this plugin.</p>
        </div>
        <div class="col-md-5 mb-md-5 mb-3"><img src="<?php echo $help_images; ?>help-create_app_2.png" class="ctf-help-img" /></div>
        <div class="col-md-5 mb-md-5 mb-3"><img src="<?php echo $help_images; ?>help-api_keys.png" class="ctf-help-img" /></div>
        <div class="col-12">
            <img src="<?php echo $help_images; ?>help-api-integrations-settings.png" class="ctf-help-img" />
        </div>
    </div>
</div>

<div class="ctf-admin-container">
    <h2>Default Available Fields for Use with Task Content Formatting</h2>
    <dl class="ctf-list">
        <dt>Your Name</dt>
            <dd>Field Tag: {%assigner%}</dd>
            <dd>Description: This will output the text string value entered into the Name field.</dd>
        <dt>Task Title</dt>
            <dd>Field Tag: {%title%}</dd>
            <dd>Description: Automatically submitted to ClickUp as the Task Name. Will output the text entered in the Task Title field.</dd>
        <dt>Priority</dt>
            <dd>Field Tag: {%priority%}</dd>
            <dd>Description: Automatically submitted to ClickUp as the task priority. Using this field tag in task content will output an integer or null value from 4 to 1. 4 being the lowest priority, 1 being the highest.</dd>
        <dt>Assign Task To</dt>
            <dd>Field Tag: {%assignees%}</dd>
            <dd>Description: Automatically submitted to ClickUp as the assigned user(s). Using this field tag will display a list of comma separated User IDs.</dd>
        <dt>Due Date</dt>
            <dd>Field Tag: {%duedate%}</dd>
            <dd>Description: Automatically submitted to Clickup as the Due Date of the task. The output for the due date in Task Content will display as: YYYY-MM-DDTHH:MM (ie. 2020-06-03T15:44 for June 6th, 2020 3:44 PM)</dd>
        <dt>Task Description</dt>
            <dd>Field Tag: {%desc%}</dd>
            <dd>Description: Automatically submitted as the Task Content and is NOT dependent on the Task Content Format. The Task Content Format will be prepended (added before) this field's submitted content.</dd>
    </dl>
</div>