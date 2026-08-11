<?php
$files = glob('database/migrations/*.php');
foreach ($files as $f) {
  if (strpos($f, 'add_performance_indexes.php') !== false) continue;
  $c = file_get_contents($f);
  $c = preg_replace('/\\s*\\->foreignId\(''tenant_id''\).*?;/', '', $c);
  $c = preg_replace('/\\s*\\->dropForeign\(\[''tenant_id''\]\);/', '', $c);
  file_put_contents($f, $c);
}
