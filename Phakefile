<?php

desc("The first name task....");
task("dependency1", function(){
  echo "*** Hello World\n";
});

task("dependency2", function(){
  echo "*** I will Second!\n";
});

desc("Run all tasks");
task("task_name", "dependency1", "dependency2", function(){
  echo "*** I will run last!\n";
});
