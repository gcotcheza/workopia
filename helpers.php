<?php

/**
 * Get the base path 
 */
 function basePath($path = ''): string
 {
    return __DIR__ . '/' . $path;
 }

 /**
  * Load a view
  */
  function loadView($name): void
  {
      $viewPath = basePath("views/{$name}.view.php");

      if (file_exists($viewPath)) {
         require $viewPath;
      } else {
         echo "View {$name} not found!";
      }
  }

   /**
  * Load a partial
  */
  function loadPartial($name): void
  {
      $prtialPath = basePath("views/partials/{$name}.php");

      if (file_exists($prtialPath)) {
         require $prtialPath;
      } else {
         echo "View {$name} not found!";
      }
  }

  /**
   * Inspect a value(s)
   */
   function inspect($value): void 
   {
      echo '<pre>';
      var_dump($value);
      echo '</pre>';
   }

   /**
   * Inspect a value(s) and die
   */
   function inspectAndDie($value): void 
   {
      echo '<pre>';
      die(var_dump($value));
      echo '</pre>';
   }