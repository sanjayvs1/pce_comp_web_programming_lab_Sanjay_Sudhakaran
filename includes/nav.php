<header class="navbar">
  <div class="container">
    <h1 class="logo"><a href="index.php">CircleFit</a></h1>
    <form class="search-form">
      <input type="text" placeholder="Search" />
      <button type="submit">Search</button>
    </form>
    <nav class="nav-links">
      <a href="index.php">Home</a>
      <a href="#">Cart</a>
      <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) : ?>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
      <?php else : ?>
        <a href="login.php">Login</a>
        <a href="signup.php">Signup</a>
      <?php endif; ?>
    </nav>
  </div>
</header>