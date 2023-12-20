<div class="content py-3">
    <div class="card rounded-0 shadow">
        <div class="card-body">
            <h3>Welcome to TD Sweets</h3>
            <hr>
            <div class="col-12">
            <div class="container">
    <div class="row g-3">
        <div class="col-md-3">
            <div class="card text-dark">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="col-auto pe-1">
                            <span class="fa fa-th-list fs-3 text-primary"></span>
                        </div>
                        <div class="col-auto flex-grow-1">
                            <div class="fs-5"><b>Categories</b></div>
                            <div class="fs-6 text-end fw-bold">
                                <?php 
                                use Vtiful\Kernel\Format;
                                $category = $conn->query("SELECT count(category_id) as `count` FROM `category_list` where delete_flag = 0 ")->fetch_array()['count'];
                                echo $category > 0 ? format_num($category) : 0 ;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-dark">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="col-auto pe-1">
                            <span class="fas fa-shopping-bag fs-3 text-secondary"></span>
                        </div>
                        <div class="col-auto flex-grow-1">
                            <div class="fs-5"><b>Products</b></div>
                            <div class="fs-6 text-end fw-bold">
                                <?php 
                                $product = $conn->query("SELECT count(product_id) as `count` FROM `product_list` where delete_flag = 0 ")->fetch_array()['count'];
                                echo $product > 0 ? format_num($product) : 0 ;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-dark">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="col-auto pe-1">
                            <span class="fa fa-file-alt fs-3 text-info"></span>
                        </div>
                        <div class="col-auto flex-grow-1">
                            <div class="fs-5"><b>Total Stocks</b></div>
                            <div class="fs-6 text-end fw-bold">
                                <?php 
                                $stock = 0;
                                $stock_query = $conn->query("SELECT * FROM `stock_list` where product_id in (SELECT product_id FROM `product_list` where delete_flag = 0) and unix_timestamp(CONCAT(`expiry_date`)) >= unix_timestamp(CURRENT_TIMESTAMP) ");
                                while($row = $stock_query->fetch_assoc()):
                                    $stock_in = $conn->query("SELECT sum(quantity) as `total` FROM `stock_list` where unix_timestamp(CONCAT(`expiry_date`, ' 23:59:59')) >= unix_timestamp(CURRENT_TIMESTAMP) and product_id = '{$row['product_id']}' ")->fetch_array()['total'];
                                    $stock_out = $conn->query("SELECT sum(quantity) as `total` FROM `transaction_items` where product_id = '{$row['product_id']}' ")->fetch_array()['total'];
                                    $stock_in = $stock_in > 0 ? $stock_in : 0;
                                    $stock_out = $stock_out > 0 ? $stock_out : 0;
                                    $qty = $stock_in-$stock_out;
                                    $qty = $qty > 0 ? $qty : 0;
                                    $stock += $qty;
                                endwhile;
                                echo $stock > 0 ? format_num($stock) : 0 ;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-dark">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="col-auto pe-1">
                            <span class="fa fa-coins fs-3 text-warning"></span>
                        </div>
                        <div class="col-auto flex-grow-1">
                            <div class="fs-5"><b>Today's Sales</b></div>
                            <div class="fs-6 text-end fw-bold">
                                <?php 
                                $sales = $conn->query("SELECT sum(total) as `total` FROM `transaction_list` where date(date_added) = date(CURRENT_TIMESTAMP) ".(($_SESSION['type'] != 1)? " and user_id = '{$_SESSION['user_id']}' " : ""))->fetch_array()[0];
                                echo($sales > 0 ? format_num($sales) :0) ;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                <hr>
                <canvas id="salesChart" width="400" height="100"></canvas>

                <hr>
                <div class="row">
                    <div class="col-12">
                        <h3>Stock Available</h3>
                        <hr>
                        <table class="table table-striped table-hover table-bordered" id="inventory">
                            <colgroup>
                                <col width="25%">
                                <col width="25%">
                                <col width="25%">
                                <col width="25%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="py-0 px-1">Category</th>
                                    <th class="py-0 px-1">Product Code</th>
                                    <th class="py-0 px-1">Product Name</th>
                                    <th class="py-0 px-1">Stock Status</th>
                                    <th class="py-0 px-1">Available Quantity</th>
                                    <th class="py-0 px-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $sql = "SELECT p.*,c.name as cname FROM `product_list` p inner join `category_list` c on p.category_id = c.category_id where p.status = 1 and p.delete_flag = 0 order by `name` asc";
                                    $qry = $conn->query($sql);
                                    while($row = $qry->fetch_assoc()):
                                        $stock_in = $conn->query("SELECT sum(quantity) as `total` FROM `stock_list` where unix_timestamp(CONCAT(`expiry_date`, ' 23:59:59')) >= unix_timestamp(CURRENT_TIMESTAMP) and product_id = '{$row['product_id']}' ")->fetch_array()['total'];
                                        $stock_out = $conn->query("SELECT sum(quantity) as `total` FROM `transaction_items` where product_id = '{$row['product_id']}' ")->fetch_array()['total'];
                                        $stock_in = $stock_in > 0 ? $stock_in : 0;
                                        $stock_out = $stock_out > 0 ? $stock_out : 0;
                                        $qty = $stock_in-$stock_out;
                                        $qty = $qty > 0 ? $qty : 0;
                                ?>
                                    <tr class="">
                                        <td class=""><?php echo $row['cname'] ?></td>
                                        <td class=""><?php echo $row['product_code'] ?></td>
                                        <td class=""><?php echo $row['name'] ?></td>
                                        <td class=""><?php echo $qty <= 0? '<span class="badge rounded-pill bg-danger">Out of stock</span>' : '<span class="badge rounded-pill bg-success">Available</span>' ?></td>
                                        <td class=" text-end">
                                           
                                            <?php echo $qty ?></td>
                                            <td> <?php  if($_SESSION['type'] == 1): ?>
                                            <?php echo $qty < $row['alert_restock']? "<a href='javascript:void(0)' class='btn btn-warning btn-sm restock me-1' data-pid = '".$row['product_id']."' data-name = '".$row['product_code'].' - '.$row['name']."'> Restock</a>":'' ?>
                                            <?php endif; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Assuming you have a database connection established and named $conn

// Generate an array of dates for the last 7 days (including today)
$dates = [];
for ($i = 6; $i >= 0; $i--) {
    $dates[] = date('Y-m-d', strtotime("-$i days"));
}

// Initialize an array to store the sales data
$salesArray = [];

// Fetch sales data for each date and store it in $salesArray
foreach ($dates as $date) {
    $query = "SELECT IFNULL(SUM(total), 0) AS total_sales
              FROM transaction_list
              WHERE DATE(date_added) = '$date'";
    
    $result = mysqli_query($conn, $query); // Changed $connection to $conn

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $salesArray[$date] = round($row['total_sales'],2);
        mysqli_free_result($result);
    } else {
        echo "Query failed: " . mysqli_error($conn); // Changed $connection to $conn
    }
}

?>




<script>
    $(function(){
        $('.restock').click(function(){
            uni_modal('Add New Stock for <span class="text-primary">'+$(this).attr('data-name')+"</span>","manage_stock.php?pid="+$(this).attr('data-pid'))
        })
        $('table#inventory').dataTable()

    })
</script>

<script>
// Sample sales data for 7 days
const salesData = [<?php echo implode(',',$salesArray);?>];

// Get a reference to the canvas element
const ctx = document.getElementById('salesChart').getContext('2d');

// Create the chart
<?php

// Get the current date
$currentDate = date('Y-m-d');
$dates = [];
// Print the dates for the last 6 days
$check =0;
for ($i = 0; $i <= 6; $i++) {
    $date = date('Y-m-d', strtotime("-$i days"));
   // $i=$i-1;
   $check = $i;
   $notice = "($check days ago)";
   if($i==0){
    $notice = "(today)";
   }
   if($i==1){
    $notice = "(yesterday)";
   }
    $dates[]=date("jS M", strtotime($date))." $notice";
}

$dates = array_reverse($dates);
$imploded = implode("','", $dates);
$result = "['" . $imploded . "']";
?>
const salesChart = new Chart(ctx, {
  type: 'bar', // Specify the chart type as a bar chart
  data: {
    labels: <?php echo $result;?>, // Labels for the X-axis
    datasets: [
      {
        label: 'Revenue',
        data: salesData, // Sales data values
        backgroundColor: 'rgba(75, 192, 192, 0.5)', // Bar color
        borderColor: 'rgba(75, 192, 192, 1)', // Border color
        borderWidth: 1, // Border width
      },
    ],
  },
  options: {
    scales: {
      y: {
        beginAtZero: true, // Start the Y-axis at zero
      },
    },
  },
});
</script>
