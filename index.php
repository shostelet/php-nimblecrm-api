<?php
require_once('nimble.php');
$nimble = new NimbleApi();
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Nimble API php implementation ugly demo</title>
  </head>
  <body>
    <?php if($_SESSION['nimble']['access_token'] && $_SESSION['nimble']['expires_in'] < time()): ?>
      <?php
      $contact_list = $nimble->getContactList($_SESSION['nimble']['access_token']);
      ?>
      <?php foreach($contact_list['resources'] as $contact): ?>
        <?php var_dump($contact)?>
        <br />
          ############
        <br />
      <?php endforeach; ?>
    <?php elseif($_GET['code']): ?>
      <?php
      session_start();
      $output = $nimble->requestAccessToken($_GET['code']);
      
      $response['access_token'] = isset($output['access_token']) ? $output['access_token'] : '';
      $response['expires_in'] = isset($output['expires_in']) ? $output['expires_in'] : '';
      $response['refresh_token'] = isset($output['refresh_token']) ? $output['refresh_token'] : '';      
      $_SESSION['nimble'] = $response;
      
      var_dump($output);
      ?>
      Reload me !
    <?php else: ?>
      Please visit <?php echo $nimble->requestAuthGrantCodeUrl() ?>
    <?php endif; ?>
  </body>
</html>
