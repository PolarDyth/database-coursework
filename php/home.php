<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/cw2/php/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/cw2/php/components/auth/auth.php';

$conn = DBConnect();

$hasFilters = isset($_GET['cuisine_id']) || isset($_GET['min_rating']) || isset($_GET['location']);

if (!$hasFilters && !empty($user['preferences'])) {
  $prefCuisineIds = explode(',', $user['preferences']);
  $queryParams = [];

  foreach ($prefCuisineIds as $id) {
    $queryParams[] = "cuisine_id[]=" . urlencode(trim($id));
  }

  $queryString = implode("&", $queryParams);
  header("Location: /database/home?$queryString");
  exit();
}


$cuisineStmt = $conn->prepare("SELECT id, name FROM Cuisines");
$cuisineStmt->execute();
$cuisines = $cuisineStmt->get_result();

$selectedCuisines = $_GET['cuisine_id'] ?? [];
$minRating = $_GET['min_rating'] ?? '';
$location = $_GET['location'] ?? '';

$query = "SELECT 
            Restaurants.id,
            Restaurants.name, 
            Restaurants.location, 
            ROUND(AVG(Reviews.rating), 1) AS average_rating,
            GROUP_CONCAT(DISTINCT Cuisines.name SEPARATOR ', ') AS cuisine
          FROM Restaurants
          JOIN restaurantcuisine ON Restaurants.id = restaurantcuisine.restaurantID
          JOIN Cuisines ON restaurantcuisine.cuisineID = Cuisines.id
          LEFT JOIN Reviews ON Restaurants.id = Reviews.restaurant_id
          WHERE 1";

$params = [];
$types = "";

if (!empty($selectedCuisines)) {
  $inClause = implode(',', array_fill(0, count($selectedCuisines), '?'));
  $query .= " AND Restaurants.id IN (
    SELECT restaurantID
    FROM restaurantcuisine
    WHERE cuisineID IN ($inClause)
  )";
  $params = array_merge($params, $selectedCuisines);
  $types .= str_repeat("i", count($selectedCuisines));
}

if (!empty($location)) {
  $query .= " AND Restaurants.location LIKE ?";
  $params[] = "%" . $location . "%";
  $types .= "s";
}

$havingClause = "";

if (!empty($minRating)) {
  $havingClause .= " HAVING AVG(Reviews.rating) >= ?";
  $params[] = $minRating;
  $types .= "d";
}

$query .= " GROUP BY Restaurants.id" . $havingClause;

$stmt = $conn->prepare($query);
if (!empty($params)) {
  $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$results = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Explore Restaurants</title>
  <link rel="stylesheet" href="/database/css/home.css">
</head>

<body>

  <?php include($_SERVER['DOCUMENT_ROOT'] . '/database/components/nav.php'); ?>

  <section class="container">
    <h2 class="section-title">Explore Restaurants</h2>

    <form method="get" action="home" class="filter-form">
      <?php
      $selectedCuisines = $_GET['cuisine_id'] ?? [];
      if (!is_array($selectedCuisines)) {
        $selectedCuisines = [$selectedCuisines];
      }
      ?>
      <fieldset class="filter-group">
        <legend>Cuisines</legend>
        <?php while ($cuisine = $cuisines->fetch_assoc()): ?>
          <label>
            <input type="checkbox" name="cuisine_id[]" value="<?= $cuisine['id'] ?>"
              <?= in_array((string)$cuisine['id'], $selectedCuisines) ? 'checked' : '' ?>>
            <?= htmlspecialchars($cuisine['name']) ?>
          </label>
        <?php endwhile; ?>
      </fieldset>

      <div class="filter-group">
        <label for="min_rating">Min Rating:</label>
        <input type="number" step="0.1" min="1" max="5" name="min_rating" id="min_rating" value="<?php echo htmlspecialchars($minRating); ?>">
      </div>

      <div class="filter-group">
        <label for="location">Location:</label>
        <input type="text" name="location" id="location" value="<?php echo htmlspecialchars($location); ?>">
      </div>

      <button type="submit" class="btn">Apply Filters</button>
      <a href="/database/home?min_rating=&location=" class="btn reset-btn">Reset Filters</a>
    </form>

    <div class="restaurant-cards">
      <?php if ($results->num_rows > 0): ?>
        <?php while ($row = $results->fetch_assoc()): ?>
          <div class="restaurant-card">
            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
            <p><strong>Cuisine:</strong> <?php echo htmlspecialchars($row['cuisine']); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
            <p><strong>Rating:</strong> <?php echo htmlspecialchars($row['average_rating']); ?> / 5.0</p>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="no-results">No restaurants found matching your criteria.</p>
      <?php endif; ?>
    </div>
  </section>

  <?php include($_SERVER['DOCUMENT_ROOT'] . '/database/components/footer.php'); ?>

</body>

</html>