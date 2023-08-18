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
$expressionErr = "";
$expression = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["expression"])) {
    $expressionErr = "Field is required";
  } else {
    $expression = test_input($_POST["expression"]);
  }
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function evaluateExpression($expression) {
    $outputQueue = [];
    $operatorStack = [];

    $operators = ['+', '-', '*', '/'];

    $tokens = preg_split('/([\+\-\*\/\(\)])/', $expression, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

    foreach ($tokens as $token) {
        if (is_numeric($token)) {
            $outputQueue[] = $token;
        } elseif (in_array($token, $operators)) {
            while (!empty($operatorStack) && in_array(end($operatorStack), $operators) && precedence($token) <= precedence(end($operatorStack))) {
                $outputQueue[] = array_pop($operatorStack);
            }
            $operatorStack[] = $token;
        } elseif ($token == '(') {
            $operatorStack[] = $token;
        } elseif ($token == ')') {
            while (end($operatorStack) != '(') {
                $outputQueue[] = array_pop($operatorStack);
            }
            array_pop($operatorStack);
        }
    }

    while (!empty($operatorStack)) {
        $outputQueue[] = array_pop($operatorStack);
    }

    $stack = [];
    foreach ($outputQueue as $token) {
        if (is_numeric($token)) {
            array_push($stack, $token);
        } elseif (in_array($token, $operators)) {
            $b = array_pop($stack);
            $a = array_pop($stack);
            switch ($token) {
                case '+':
                    array_push($stack, $a + $b);
                    break;
                case '-':
                    array_push($stack, $a - $b);
                    break;
                case '*':
                    array_push($stack, $a * $b);
                    break;
                case '/':
                    array_push($stack, $a / $b);
                    break;
            }
        }
    }

    return $stack[0];
}

function precedence($operator) {
    if ($operator == '+' || $operator == '-') {
        return 1;
    } elseif ($operator == '*' || $operator == '/') {
        return 2;
    }
    return 0;
}
?>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
  Expression: <input type="text" name="expression" placeholder="(1+(4+5+2)*3)-(6+8)" value="<?php echo $expression;?>">
  <span class="error"> <?php echo $expressionErr;?></span>
 
  <br><br>
  <input type="submit" name="submit" value="Calculate">  
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($expressionErr)) {
    $result = evaluateExpression($expression);
    echo "<h2>Result:</h2>";
    echo $result;
}
?>

</body>
</html>
