<?php
@session_start();
$date = gmdate("d-M-Y", time()+3600*+7);
$now = md5($date.$_SESSION['user']);
if($compare!=$now)
{
	?>
	<script>alert('Your ticket checkout is expired!'); document.location.href='<?=base_url()?>index.php/user/shopping';</script>
	<?php
}
else 
{
?>
<h3>Checkout</h3>
<table width="525" border="0" cellspacing="2" cellpadding="2">
  <tr>
    <td width="29">Qty</td>
    <td width="218">Nama Barang</td>
    <td width="107">Harga</td>
    <td width="120">Sub-Total</td>
  </tr>
  <?php
  foreach($query->result_array() as $row)
  {
  ?>
  <tr>
    <td><?=$row['qty']?></td>
    <td><?=$row['nama_barang']?></td>
    <td><?=rupiah($row['harga_barang'])?></td>
    <td><?=rupiah($row['sub_total'])?></td>
  </tr>
  <?php
  }
  foreach($sum->result_array() as $total)
  {
  ?>
  <tr>
    <td colspan="3" align="center">Total</td>
    <td><?=rupiah($total['total'])?></td>
  </tr>
  <?php
  }
  ?>
</table>
<br />
<hr align="center" width='100' />
<?php
}
?>