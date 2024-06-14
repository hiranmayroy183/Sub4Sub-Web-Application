<?php include 'functions/header.php' ?>

<div class="container mt-3">
  <h2>Bordered Table</h2>
  <p>The .table-bordered class adds borders on all sides of the table and the cells:</p>            
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Username</th>
        <th>Channel Name</th>
        <th>Channel URL</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>John</td>
        <td>Doe</td>
        <td><a href="" > <button type="button" class="btn btn-primary">Open</button> </a></td>
      </tr>
      <tr>
        <td>Mary</td>
        <td>Moe</td>
        <td><a href="" > <button type="button" class="btn btn-primary">Open</button> </a></td>
      </tr>
      <tr>
        <td>July</td>
        <td>Dooley</td>
        <td><a href="" > <button type="button" class="btn btn-primary">Open</button> </a></td>
      </tr>
    </tbody>
  </table>
</div>


<?php include 'functions/footer.php' ?>