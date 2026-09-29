<?php
$values = [
  'name' => '',
  'email' => '',
  'age' => '',
  'relationship' => '',
  'ideal_date' => '',
  'green_flag' => '',
];
$errors = [];
$submitted = false;
$sendError = false;
$ownerEmail = 'frankrolin2@gmail.com';
$ownerPhone = '+254115108214';

function escape(string $value): string
{
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function oldValue(string $key, array $values): string
{
  return escape($values[$key] ?? '');
}

function fieldError(string $key, array $errors): string
{
  return isset($errors[$key]) ? '<span class="field-error">' . escape($errors[$key]) . '</span>' : '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach ($values as $key => $value) {
    $values[$key] = trim((string) ($_POST[$key] ?? ''));
  }

  if ($values['name'] === '') {
    $errors['name'] = 'Please add your name.';
  }

  if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Enter a valid email address.';
  }

  if (!filter_var($values['age'], FILTER_VALIDATE_INT) || (int) $values['age'] < 18) {
    $errors['age'] = 'You must be 18 or older to apply.';
  }

  if ($values['relationship'] === '') {
    $errors['relationship'] = 'Choose what you are looking for.';
  }

  if ($values['ideal_date'] === '') {
    $errors['ideal_date'] = 'Tell us about your ideal first date.';
  }

  if ($values['green_flag'] === '') {
    $errors['green_flag'] = 'Share one green flag about yourself.';
  }

  if (!isset($_POST['consent'])) {
    $errors['consent'] = 'Please confirm before submitting.';
  }

  if (empty($errors)) {
    $relationshipLabels = [
      'something-serious' => 'Something meaningful',
      'see-where-it-goes' => 'See where it goes',
      'new-connection' => 'A new connection',
    ];
    $application = [
      'Name' => $values['name'],
      'Email' => $values['email'],
      'Age' => $values['age'],
      'Looking for' => $relationshipLabels[$values['relationship']] ?? $values['relationship'],
      'Ideal first date' => $values['ideal_date'],
      'Green flag' => $values['green_flag'],
    ];
    $message = "A new application has been submitted.\r\n\r\n";
    foreach ($application as $label => $answer) {
      $message .= $label . ":\r\n" . $answer . "\r\n\r\n";
    }

    $serverName = preg_replace('/[^a-zA-Z0-9.-]/', '', $_SERVER['SERVER_NAME'] ?? 'localhost') ?: 'localhost';
    $headers = [
      'From' => 'The Girlfriend Application <no-reply@' . $serverName . '>',
      'Reply-To' => $values['email'],
      'Content-Type' => 'text/plain; charset=UTF-8',
    ];
    $submitted = mail($ownerEmail, 'New girlfriend application', $message, $headers);
    $sendError = !$submitted;
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#f8eeee">
  <title>The Girlfriend Application</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <script src="script.js" defer></script>
</head>

<body>
  <main class="page-shell">
    <aside class="intro-panel" aria-labelledby="page-title">
      <a class="wordmark" href="#top" aria-label="The Girlfriend Application home">
        <span class="wordmark-mark" aria-hidden="true">g.</span>
        <span>GOOD TOGETHER</span>
      </a>

      <div class="intro-copy" id="top">
        <p class="eyebrow"><span class="eyebrow-dot"></span> OPEN APPLICATIONS</p>
        <h1 id="page-title">Looking for<br>my <em>favorite</em><br>person.</h1>
        <p class="intro-description">A little form for a big possibility. Tell me who you are, what makes you laugh, and where we should go for that first coffee.</p>
      </div>

      <figure class="date-photo">
        <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1000&q=85" alt="A warmly lit restaurant ready for a date">
        <figcaption><span>THE FIRST DATE</span><span>Somewhere with good dessert <span aria-hidden="true">↗</span></span></figcaption>
      </figure>

      <p class="side-note"><span aria-hidden="true">✳</span> Made for good conversations and mutual enthusiasm.</p>
      <div class="contact-details" aria-label="Contact the owner">
        <a href="tel:<?= escape($ownerPhone) ?>"><?= escape($ownerPhone) ?></a>
        <a href="mailto:<?= escape($ownerEmail) ?>"><?= escape($ownerEmail) ?></a>
      </div>
    </aside>

    <section class="form-panel" aria-label="Application form">
      <div class="form-topline">
        <span>THE APPLICATION</span>
        <span>01 <span class="topline-slash">/</span> 04</span>
      </div>

      <?php if ($submitted): ?>
        <div class="success-message" role="status" tabindex="-1">
          <span class="success-icon" aria-hidden="true">&#10003;</span>
          <p class="eyebrow">APPLICATION RECEIVED</p>
          <h2>Well, this is<br><em>exciting.</em></h2>
          <p>Thanks for putting yourself out there, <?= escape($values['name']) ?>. Your application was sent to <?= escape($ownerEmail) ?>.</p>
          <a class="reset-link" href="index.php">Start again <span aria-hidden="true">&#8594;</span></a>
        </div>
      <?php else: ?>
        <div class="form-heading">
          <p class="eyebrow">FIRST, THE BASICS</p>
          <h2>Tell me a little<br>about <em>you.</em></h2>
          <p class="form-intro">No perfect answers here. Just be yourself.</p>
        </div>

        <?php if (!empty($errors) || $sendError): ?>
          <p class="form-alert" role="alert"><?= $sendError ? 'Your application could not be sent right now. Please try again or contact the owner directly.' : 'A couple of details need your attention below.' ?></p>
        <?php endif; ?>

        <form method="post" action="index.php" novalidate>
          <div class="field-grid">
            <div class="field">
              <label for="name">Your name <span>*</span></label>
              <input id="name" name="name" type="text" autocomplete="name" placeholder="What should I call you?" value="<?= oldValue('name', $values) ?>" aria-describedby="name-error" required>
              <?= fieldError('name', $errors) ?>
            </div>

            <div class="field">
              <label for="email">Email address <span>*</span></label>
              <input id="email" name="email" type="email" autocomplete="email" placeholder="you@example.com" value="<?= oldValue('email', $values) ?>" aria-describedby="email-error" required>
              <?= fieldError('email', $errors) ?>
            </div>

            <div class="field">
              <label for="age">Your age <span>*</span></label>
              <input id="age" name="age" type="number" min="18" max="120" inputmode="numeric" placeholder="18+" value="<?= oldValue('age', $values) ?>" aria-describedby="age-hint age-error" required>
              <span class="field-hint" id="age-hint">This form is for adults (18+) only.</span>
              <?= fieldError('age', $errors) ?>
            </div>

            <div class="field">
              <label for="relationship">What are you looking for? <span>*</span></label>
              <div class="select-wrap">
                <select id="relationship" name="relationship" aria-describedby="relationship-error" required>
                  <option value="" disabled <?= $values['relationship'] === '' ? 'selected' : '' ?>>Choose one</option>
                  <option value="something-serious" <?= $values['relationship'] === 'something-serious' ? 'selected' : '' ?>>Something meaningful</option>
                  <option value="see-where-it-goes" <?= $values['relationship'] === 'see-where-it-goes' ? 'selected' : '' ?>>See where it goes</option>
                  <option value="new-connection" <?= $values['relationship'] === 'new-connection' ? 'selected' : '' ?>>A new connection</option>
                </select>
              </div>
              <?= fieldError('relationship', $errors) ?>
            </div>

            <div class="field field-wide">
              <label for="ideal_date">Your ideal first date <span>*</span></label>
              <textarea id="ideal_date" name="ideal_date" rows="3" maxlength="240" placeholder="The place, the plan, the little details..." aria-describedby="ideal-date-count ideal-date-error" required><?= oldValue('ideal_date', $values) ?></textarea>
              <div class="field-meta">
                <span class="field-hint">Keep it simple or get wonderfully specific.</span>
                <span class="character-count" id="ideal-date-count">0 / 240</span>
              </div>
              <?= fieldError('ideal_date', $errors) ?>
            </div>

            <div class="field field-wide">
              <label for="green_flag">A green flag about you <span>*</span></label>
              <input id="green_flag" name="green_flag" type="text" maxlength="100" placeholder="I always remember the little things..." value="<?= oldValue('green_flag', $values) ?>" aria-describedby="green-flag-count green-flag-error" required>
              <div class="field-meta">
                <span class="field-hint">A tiny brag is absolutely allowed.</span>
                <span class="character-count" id="green-flag-count">0 / 100</span>
              </div>
              <?= fieldError('green_flag', $errors) ?>
            </div>
          </div>

          <div class="consent-row">
            <input id="consent" name="consent" type="checkbox" value="yes" <?= isset($_POST['consent']) ? 'checked' : '' ?> aria-describedby="consent-error" required>
            <label for="consent">I confirm I am 18 or older, and I am happy to be contacted about my application.</label>
          </div>
          <?= fieldError('consent', $errors) ?>

          <div class="submit-row">
            <button type="submit">Send my application <span aria-hidden="true">&#8594;</span></button>
            <p><span aria-hidden="true">&#9829;</span> No pressure. Just a beginning.</p>
          </div>
        </form>

        <p class="privacy-note">Applications are emailed to <?= escape($ownerEmail) ?> and are not stored by this form.</p>
      <?php endif; ?>
    </section>
  </main>
</body>

</html>