<?php
require_once('nimble.php');
$nimble = new ApiNimble();
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
      $output = $nimble->requestAccessToken($_GET['code']);
      var_dump($output);
      ?>
      Reload me !
    <?php else: ?>
      Please visit <?php echo $nimble->requestAuthGrantCodeUrl() ?>
    <?php endif; ?>
  </body>
</html>