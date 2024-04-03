<header class="navbar">
  <h1 class="logo"><a href="index.php">CircleFit</a></h1>
  <form action="search.php" method="GET"  class="search-form">
        <input type="text" id="search_query" name="search_query" />
        <button type="submit">Search</button>
    </form>
  <nav class="nav-links">
    <a href="index.php">Home</a>
    <?php if (isset ($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
      <a href="cart.php">Cart</a>
      <a href="orders.php">Orders</a>
      <a href="profile.php">Profile</a>
      <?php if (isset ($_SESSION["loggedin"]) && $_SESSION["UserType"] === "Seller"): ?>
        <a href="dashboard.php">Dashboard</a>
        <a href="pendingOrders.php">Pending Orders</a>
      <?php endif; ?>
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="login.php">Login</a>
      <a href="signup.php">Signup</a>
    <?php endif; ?>
  </nav>
</header>