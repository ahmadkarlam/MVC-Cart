<?php @session_start(); ?>
<h3>Shopping</h3>
<br>
<fieldset style="width:500px">
<div class="cart_list">  
        <h3>Keranjang</h3><span id='show' style='cursor:pointer;display:none;'>show...</span>  
        <div id="cart_content">  
                <?php @session_start(); if (empty($_SESSION['user'])){?>
        Anda harus login terlebih dahulu jika ingin membeli
        <p><?=anchor('index.php/home/signup','Klik disini untuk signup.')?></p>
        <?php } else { 
        	$date = gmdate('d-M-Y', time()+3600*+7);
        	$user = $_SESSION['user'];
        	$q = $this->db->query("SELECT * FROM tb_cart where date='$date' and user='$user'");
        	$sum = $this->db->query("SELECT sub_total, SUM(sub_total) as total FROM tb_cart where date='$date' and user='$user'");
        	if($q->num_rows()==0):  
    echo 'Keranjang masih kosong.';  
else:  
?>  
  
<?php echo form_open('index.php/user/shopping'); ?>  
<table width="100%" cellpadding="0" cellspacing="0">  
    <thead>  
        <tr>  
            <td width="11%">Qty</td>  
            <td width="36%">Nama Barang</td>  
            <td width="28%">Harga</td>  
            <td width="25%">Sub-Total</td>  
        </tr>  
    </thead>  
    <tbody>  
        <?php foreach($q->result_array() as $items): ?>
        <tr>  
            <td align='center'>  
                <?php echo form_input(array('name' => 'qty_e', 'value' => $items['qty'], 'maxlength' => '3', 'size' => '5')); ?>
                <?=form_submit('edit', 'Edit')?>
            <input name="id_cart" type="hidden" id="id_cart" value="<?=$items['id_cart']?>" /></td>  
              
            <td><?=$items['nama_barang']?></td>  
              
            <td><?php echo rupiah($items['harga_barang']); ?><input name="harga_barang_e" type="hidden" id="harga_barang_e" value="<?=$items['harga_barang']?>" /></td>  
            <td><?php echo rupiah($items['sub_total']); ?></td>  
        </tr>  
        <?php endforeach; ?>
                <?php foreach($sum->result_array() as $row){?>
        <tr>  
            <td></td>  
            <td></td>  
            <td><strong>Total</strong></td>  
            <td>
            <?php echo rupiah($row['total']); ?>
            <input name="total_e" type="hidden" id="total_e" value="<?=$row['total']?>" />
            </td>  
        </tr>  
    </tbody>
                <?php } ?>
            
</table>  
  
<p><?=form_submit('empty', 'Empty Cart')?> | <?=form_submit('check', 'Check Out')?></p>
<p><small>Jika quantity di isi 0 maka barang tersebut akan dihapus dari cart/keranjang.</small></p>    
<?php   
echo form_close();   
endif;  
}
?>  
        </div>  
    </div>
</fieldset>
<p id='hide' style='cursor:pointer;' align='center'>hide...</p>
<?php if (isset($_SESSION['user'])){?>
<p>Pilih yang anda suka , selamat berbelanja :)</p>
<?php } ?>
<div class="paging"><?php echo $paging; ?></div>
<?php 
foreach($tampil as $row)
{
	$query = $this->db->query("select * from tb_merk where id_merk='$row[id_merk]'");
	foreach($query->result_array() as $merk)
	{
?>
<?php 
if(isset($_SESSION['user'])){
echo form_open('index.php/user/shopping');
}
?>
<fieldset style="width:500px">
<legend><?php echo $merk['merk']; ?></legend>
<table width="500" border="0" cellspacing="2" cellpadding="2">
  <tr>
    <td width="200" rowspan="3"><img src="<?=base_url()?>webroot/img/barang/<?=$row['photo']?>" alt="photo_<?=substr(md5($row['id_barang']),0,5)?>" width="200" height="200" /></td>
    <td width="286" height="68" align="center"><strong><span style="font-size:14px;"><?=$row['nama_barang']?>
      <input name="nama_barang" type="hidden" id="nama_barang" value="<?=$row['nama_barang']?>" />
      <input name="merk_id" type="hidden" id="merk_id" value="<?=$merk['id_merk']?>" />
    </span></strong></td>
  </tr>
  <tr>
    <td height="57" align="center">Price : <?=rupiah($row['harga_barang'])?>
      <strong><span style="font-size:14px;">
      <input name="harga_barang" type="hidden" id="harga_barang" value="<?=$row['harga_barang']?>" />
      </span></strong></td>
  </tr>
  <tr>
    <td height="91" align="center">
    <?php
	if(isset($_SESSION['user']))
	{
	?>
    Qty : <input name="qty" type="text" id="qty" size="3" maxlength="2" />
    <?php echo form_submit('add_to_cart', 'Add to Cart'); ?>
    <?php
	}
	?>
    </td>
  </tr>
</table>

</fieldset>
<?php 
if(isset($_SESSION['user'])){
echo form_close();
}
?>
<?php
	}
}
?>
<br>
<div class="paging"><?php echo $paging; ?></div>
<br>
<script>
$(document).ready(function(){
	$('#hide').click(function(){
		$('#cart_content').slideUp('slow');
		$('#cart_content').fadeOut('slow');
		$('#hide').fadeOut('slow');
		$('#show').fadeIn('slow');
	});
	$('#show').click(function(){
		$('#cart_content').slideDown('slow');
		$('#cart_content').fadeIn('slow');
		$('#hide').fadeIn('slow');
		$('#show').fadeOut('slow');
	});
});
</script>