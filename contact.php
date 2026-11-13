<?php
$pageTitle = 'Contact Us | Paxon Industries';
include 'includes/header.php';
$status = $_GET['status'] ?? '';
?>

<main>
  <section class="section contact">
    <h1 class="section-title">Request a Quote</h1>

    <?php if ($status === 'success'): ?>
      <div class="alert alert-success">Thank you! We have received your inquiry and will get back to you soon.</div>
    <?php elseif ($status === 'error'): ?>
      <div class="alert alert-error">Please fill in all required fields with valid details and try again.</div>
    <?php endif; ?>

    <form id="inquiryForm" action="handlers/submit-inquiry.php" method="POST" novalidate>
      <div class="form-grid">
        <div class="form-group">
          <label for="name">Full Name *</label>
          <input type="text" id="name" name="name" maxlength="100" required>
        </div>
        <div class="form-group">
          <label for="company">Company</label>
          <input type="text" id="company" name="company" maxlength="150">
        </div>
        <div class="form-group">
          <label for="email">Email *</label>
          <input type="email" id="email" name="email" maxlength="150" required>
        </div>
        <div class="form-group">
          <label for="phone">Phone</label>
          <input type="text" id="phone" name="phone" maxlength="30">
        </div>
        <div class="form-group">
          <label for="country">Country</label>
          <input type="text" id="country" name="country" maxlength="80">
        </div>
        <div class="form-group">
          <label for="product">Product of Interest</label>
          <select id="product" name="product">
            <option value="Dehydrated Onion">Dehydrated Onion</option>
            <option value="Dehydrated Garlic">Dehydrated Garlic</option>
            <option value="Other">Other</option>
          </select>
        </div>
        <div class="form-group full">
          <label for="quantity">Approx. Quantity Required</label>
          <input type="text" id="quantity" name="quantity" maxlength="100" placeholder="e.g. 5 tonnes per month">
        </div>
        <div class="form-group full">
          <label for="message">Message *</label>
          <textarea id="message" name="message" rows="5" required></textarea>
        </div>
      </div>

      <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off">

      <p id="formError" class="form-error" hidden>Please fill in all required fields and enter a valid email.</p>
      <button type="submit" class="btn">Send Inquiry</button>
    </form>
  </section>
</main>

<?php include 'includes/footer.php'; ?>