/**
 * login-configurator.js - Javascript for the Settings page.
 *
 * @package Login Configurator
 * @subpackage includes
 * @author GrandSlambert
 * @copyright 2009-2013
 * @access public
 * @since 0.1
 */

/* Function to change tabs on the settings pages */
function login_configurator_show_tab(tab) {
	/* Close Active Tab */
	activeTab = document.getElementById('active_tab').value;
	document.getElementById('login_configurator_box_' + activeTab).style.display = 'none';
	document.getElementById('login_configurator_' + activeTab).removeAttribute('class','login-configurator-selected');

	/* Open new Tab */
	document.getElementById('login_configurator_box_' + tab).style.display = 'block';
	document.getElementById('login_configurator_' + tab).setAttribute('class','login-configurator-selected');
	document.getElementById('active_tab').value = tab;
}

/* Function to verify selection to reset options */
function verifyResetOptions(element) {
	if (element.checked) {
		if (prompt('Are you sure you want to reset all of your options? To confirm, type the word "reset" into the box.') == 'reset' ) {
			document.getElementById('login_configurator_settings').submit();
		} else {
			element.checked = false;
		}
	}
}

function onClickRedirectHome(element)
{
	var field = document.getElementById('login_configurator_redirect_url');
	var url = document.getElementById('home_page_url').value;

	if (element.checked)
	{
		field.value = url;
		field.readOnly = true;
	}
	else
	{
		field.readOnly = false;
	}
}
