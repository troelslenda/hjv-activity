<?php 

function hjv_auth_scrape_settings_form($form_state) {

  $form['hjv_username'] = array(
    '#type' => 'textfield',
    '#title' => t('Employee number'),
    '#description' => t('Enter the Employee number of the user which the cronjob is using to fetch data from hjv.dk'),
    '#default_value' => variable_get('hjv_username','xxxxxx'),
  );
  $form['hjv_password'] = array(
    '#type' => 'textfield',
    '#title' => t('Password'),
    '#description' => t('The password'),
    '#default_value' => variable_get('hjv_password','xxxxxx'),
  );
  $form['hjv_activity_update_frequency'] = array(
    '#type' => 'textfield',
    '#title' => t('Opdaterings frekvens for aktiviteter'),
    '#description' => t('Indsæt frekvens for opdatering af aktiviteter'),
    '#default_value' => variable_get('hjv_activity_update_frequency',0),
    '#size' => 2,
    '#maxlength' => 2,
  );
  $form['hjv_participant_update_frequency'] = array(
    '#type' => 'textfield',
    '#title' => t('Opdaterings frekvens for aktivitets deltagere'),
    '#description' => t('Indsæt frekvens for opdatering af aktivitets deltagere. Angiv hvor mange timer skal der gå imellem. Deltager opdaterings frekvensen bør være hyppigere end aktiviteterne i sig selv. En god frekvens er hver sjette time.'),
    '#default_value' => variable_get('hjv_participant_update_frequency',0),
    '#size' => 2,
    '#maxlength' => 2,
  );
  return system_settings_form($form);
}


?>