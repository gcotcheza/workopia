<?php

/**
 * Get the base path.
 */
function basePath(string $path = ''): string
{
   return __DIR__ . '/' . $path;
}

/**
 * Load a view.
 */
function loadView(string $name, array $data = []): void
{
   $viewPath = basePath("App/views/{$name}.view.php");

   if (file_exists($viewPath)) {
      extract($data);
      require $viewPath;
   } else {
      echo "View {$name} not found!";
   }
}

/**
 * Load a partial.
 */
function loadPartial(string $name,  array $data =[]): void
{
   $prtialPath = basePath("App/views/partials/{$name}.php");

   if (file_exists($prtialPath)) {
      extract($data);
      require $prtialPath;
   } else {
      echo "View {$name} not found!";
   }
}

/**
 * Inspect a value(s).
 */
function inspect(mixed $value): void
{
   echo '<pre>';
   var_dump($value);
   echo '</pre>';
}

/**
 * Inspect a value(s) and die.
 */
function inspectAndDie(mixed $value): void
{
   echo '<pre>';
   die(var_dump($value));
   echo '</pre>';
}

/**
 * Format salary.
 */
function formatSalary(string $salary): string
{
   return '€ ' . number_format(floatval($salary), 2, ',', '.');
}

/** 
 * Sanitize data.
 */
function sanitize(string $dirty): string
{
   return filter_var(trim($dirty), FILTER_SANITIZE_SPECIAL_CHARS);
}

/**
 * Redirect to a given url.
 */
function redirect($url)
{
   header("Location: {$url}");
   exit;
}
