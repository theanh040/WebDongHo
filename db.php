<?php
$servername = "localhost:3306";
$username = "root";
$password = "";
$dbname = "jewelry";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// $sql = "insert into tbkhachhang (makh, tenkh,tuoi,gioitinh,sodienthoai,diachi) values ('kh1', 'nam', 20, 'nam', '0123456789', 'HN')";
// $sql = "update tbkhachhang set tenkh = 'nam1' where makh = 'kh1'";
$sql = "select makh, tenkh, sodienthoai from tbkhachhang";
$result = $conn->query($sql);
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        echo "makh: " . $row["makh"]. " - tenkh: " . $row["tenkh"]. " - sodienthoai: " . $row["sodienthoai"]. "<br>";
    }
}

$conn->close();
echo "Connected successfully";
?>
