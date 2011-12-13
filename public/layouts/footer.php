    </div>
    <div id="footer">Copyright <?php echo date("Y", time()); ?> - Marc Aertgeerts</div>
  </body>
</html>
<?php if(isset($database)) { $database->close_connection(); } ?>