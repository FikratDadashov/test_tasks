<!DOCTYPE HTML>  
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body>  

<?php
// define variables and set to empty values
$arrayErr = $goalErr = "";
$array = $goal = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["array"])) {
    $arrayErr = "Field is required";
  } else {
    $array = test_input($_POST["array"]);
  }

  if (empty($_POST["goal"])) {
    $goalErr = "Field is required";
  } else {
    $goal = test_input($_POST["goal"]);
  }
    
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function findCombinations($array, $goal) {
    $result = [];
    generateCombinations($array, $goal, 0, [], $result);
    return $result;
}

function generateCombinations($array, $remaining, $currentIndex, $currentCombination, &$result) {
    if ($remaining == 0) {
        $result[] = $currentCombination;
        return;
    }
    
    for ($i = $currentIndex; $i < count($array); $i++) {
        if ($array[$i] <= $remaining) {
            $newCombination = $currentCombination;
            $newCombination[] = $array[$i];
            generateCombinations($array, $remaining - $array[$i], $i, $newCombination, $result);
        }
    }
}
?>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
  Array numbers: <input type="text" name="array" placeholder="1,2,3,4" value="<?php echo $array;?>">
  <span class="error"> <?php echo $arrayErr;?></span>
 
  <br><br>
  Goal number: <input type="text" name="goal" placeholder="5" value="<?php echo $goal;?>">
  <span class="error"> <?php echo $goalErr;?></span>
 
  <br><br>
  <input type="submit" name="submit" value="Submit">  
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($arrayErr) && empty($goalErr)) {
    $arrayNumbers = array_map('intval', explode(',', $array)); // Convert string input to array of integers
    $goalNumber = intval($goal); // Convert goal to integer

    $result = findCombinations($arrayNumbers, $goalNumber);

    // Format the result as a string
    $resultString = json_encode($result);
    echo "<h2>Combinations:</h2>";
    echo $resultString;
}
?>


</body>
</html>
