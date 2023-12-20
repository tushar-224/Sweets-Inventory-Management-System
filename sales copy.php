<div class="card shadow rounded-0">
    <div class="card-body">
        <div class="w-100 h-100 d-flex flex-column">
            <div class="row">
                <div class="col-8">
                    <h3>Transaction</h3>
                </div>
                <div class="col-4 d-flex justify-content-end">
                    <button class="btn btn-sm btn-primary  id="transaction-save-btn" type="button">Save Transaction</button>
                </div>
                <div class="clear-fix mb-1"></div>
                <hr>
            </div>
            <style>
                #plist .item,
                #item-list tr {
                    cursor: pointer
                }

                td {
                    text-align: left;
                }
            </style>
            <div class="col-12 flex-grow-1">
                <form action="" class="h-100" id="transaction-form">
                    <div class="w-100 h-100 mx-0 row row-cols-2 bg-dark">
                        <div class="col-8 h-100 pb-2 d-flex flex-column">
                            <div>
                                <h3 class="text-light">Please select product below</h3>
                            </div>
                            <div class="flex-grow-1 d-flex flex-column bg-light bg-opacity-50">
                                <div class="form-group py-2 d-flex border-bottom">
                                    <label for="search" class="col-auto px-2 fw-bolder text-light">Search</label>
                                    <div class="flex-grow-1 col-auto pe-2">
                                        <input type="text" autocomplete="off" class="form-control form-control-sm rounded-0" id="search">
                                    </div>
                                </div>
                                <table class="table table-hover table-bordered  table-striped bg-light mb-0" id="plist">
                                    <thead>
                                        <tr>
                                            <th class="">Category</th>
                                            <th class="">Product Code</th>
                                            <th class="">Product Name</th>
                                            <th class="">Price</th>
                                            <th class="">Available Quantity</th>
                                            <th class="">GST</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT p.*,c.name as cname FROM `product_list` p inner join `category_list` c on p.category_id = c.category_id where p.status = 1 and p.delete_flag = 0 order by p.`name` asc";
                                        $qry = $conn->query($sql);
                                        while ($row = $qry->fetch_assoc()) :
                                            $stock_in = $conn->query("SELECT sum(quantity) as `total` FROM `stock_list` where unix_timestamp(`expiry_date`) >= unix_timestamp(CURRENT_TIMESTAMP) and product_id = '{$row['product_id']}' ")->fetch_assoc()['total'];
                                            $stock_out = $conn->query("SELECT sum(quantity) as `total` FROM `transaction_items` where product_id = '{$row['product_id']}' ")->fetch_assoc()['total'];
                                            $stock_in = $stock_in > 0 ? $stock_in : 0;
                                            $stock_out = $stock_out > 0 ? $stock_out : 0;
                                            $qty = $stock_in - $stock_out;
                                            $qty = $qty > 0 ? $qty : 0;
                                        ?>
                                            <tr class="item <?php echo $qty == 0 ? "bg-warning bg-opacity-25" : '' ?>" data-id="<?php echo $row['product_id'] ?>">
                                                <td class="pname"><?php echo $row['cname'] ?></td>
                                                <td class="pcode"><?php echo $row['product_code'] ?></td>
                                                <td class="name"><?php echo $row['name'] ?></td>
                                                <td class="price"><?php echo format_num($row['price']) ?></td>
                                                <td class="qty"><?php echo $qty ?></td>
                                                <td class="gst"><?php echo $row['gst'] ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-4 h-100 py-2">
                            <div class="h-100 d-flex flex-column">
                                <div class="w-100 flex-grow-1">
                                    <div class="h-100 d-flex w-100 flex-column">
                                        <div class="d-flex">
                                            <div class="fs-5 fw-bolder text-light flex-grow-1">Items</div>
                                            <div class="col-auto">
                                                <button class="btn btn-danger rounded-0 py-0" type="button" id="remove-item" disabled onclick="remove_item()"><i class="fa fa-trash"></i></button>
                                            </div>
                                        </div>
                                        <div>
                                            <table class="table table-hover table-bordered table-striped bg-light m-0">
                                                <colgroup>
                                                    <col width="20%">
                                                    <col width="20%">
                                                    <col width="20%">
                                                    <col width="20%">
                                                    <col width="20%">
                                                </colgroup>
                                                <thead>
                                                    <th class=" text-center">Qty</th>
                                                    <th class=" text-center">Product</th>
                                                    <th class=" text-center">Total</th>
                                                    <th class=" text-center">GST</th>
                                                    <th class=" text-center">Main</th>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div style="height:55vh !important" class="overflow-auto bg-light bg-opacity-75">
                                                <table class="table table-hover table-bordered  bg-light" id="item-list">
                                                    <colgroup>
                                                        <col width="20%">
                                                        <col width="20%">
                                                        <col width="20%">
                                                        <col width="20%">
                                                        <col width="20%">
                                                    </colgroup>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="pt-2">
                                            <div class="w-100 mx-0 d-flex pb-1">
                                                <div class="col-4 pe-2 fs-6 fw-bolder text-light">Total</div>
                                                <div class="flex-grow-1 bg-light fs-6 fw-bolder text-end px-2" id="total">0.00</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="sub_total" value="0">
                    <input type="hidden" name="total" value="0">
                    <input type="hidden" name="tendered_amount" value="0">
                    <input type="hidden" name="change" value="0">
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $(".quantity-input").on("input", function() {
            //compute(ntr)
            //calculate_total()
            alert("ok");
        });

        $('#search').on('input', function() {
            var _search = $(this).val().toLowerCase()
            $('#plist tbody tr').each(function() {
                var _text = $(this).text().toLowerCase()
                if (_text.includes(_search) === true) {
                    $(this).toggle(true)
                } else {
                    $(this).toggle(false)
                }
            })
        })
        $('#plist tbody tr').click(function() {
            var _tr = $(this);
            var pid = _tr.attr('data-id')
            var cname = _tr.find('.cname').text()
            var pcode = _tr.find('.pcode').text()
            var name = _tr.find('.name').text()
            var price = _tr.find('.price').text().replace(/,/gi, '')
            var max = _tr.find('.qty').text()
            var gst = _tr.find('.gst').text()
            var qty = 1
            var main_amount = 0;
            var gstcal = 0;
            if (max <= 0) {
                alert("Quantity must be greater than 0");
                return false;
            }
            _tr.find('.qty').text(parseFloat(_tr.find('.qty').text()) - 1);
            // if($('#item-list tbody tr[data-id="'+pid+'"]').length = 0){
            if ($('#item-list tbody tr[data-id="' + pid + '"]').length > 0) {
                qty += parseFloat($('#item-list tbody tr[data-id="' + pid + '"]').find('[name="quantity[]"]').val())
                $('#item-list tbody tr[data-id="' + pid + '"]').find('[name="quantity[]"]').val(qty).trigger('keydown')

                //main_amount = parseFloat($('#item-list tbody tr[data-id="'+pid+'"]').find('.main').text());
                //console.log(main_amount);
                gstcal = parseFloat(((parseFloat(price) + ((parseFloat(price) * parseFloat(gst)) / 100)) * qty)).toFixed(2);
                $('#item-list tbody tr[data-id="' + pid + '"]').find('.main').text(gstcal);
                $("#main_value").val(gstcal);

                calculate_total()
                return false;

            }
            var ntr = $("<tr tabindex='0'>")
            ntr.attr('data-id', pid)
            gstcal = (parseFloat(price) + ((parseFloat(price) * parseFloat(gst)) / 100));
            ntr.append('<td class=" align-middle"><input class="form-control text-center quantity-input" type="number" name="quantity[]" min="1" value="' + qty + '"/>' +
                '<input type="hidden" name="product_id[]" value="' + pid + '"/>' +
                '<input type="hidden" name="price[]" value="' + price + '"/>' +
                '</td>')
            ntr.append('<td class=" align-middle"><div class="fs-6 mb-0 lh-1">' + pcode + '<br/>' +
                '<span class="name">' + name + '</span></br>' +
                '(<span class="price">' + parseFloat(price).toLocaleString('en-US', {
                    style: 'decimal',
                    maximumFractionDigits: 2
                }) + '</span>)</div>' +
                '</td>');
            ntr.append('<td class=" align-middle text-end total">' + parseFloat(price).toLocaleString('en-US', {
                style: 'decimal',
                maximumFractionDigits: 2
            }) + '</td>')
            ntr.append('<td class=" align-middle text-end gst">' + parseFloat(gst).toLocaleString('en-US', {
                style: 'decimal',
                maximumFractionDigits: 2
            }) + '</td>')


            ntr.append('<td class=" align-middle text-end main" id="main">' + parseFloat(gstcal) + '</td>')

            $('#item-list tbody').append(ntr)
            compute(ntr, gst)
            calculate_total()
        })



        $('#transaction-save-btn').click(function() {
            if ($('#item-list tbody tr').length <= 0) {
                alert("Please add atleast 1 item first.")
                return false;
            }
            uni_modal("Payment", "tender_amount.php?amount=" + $('[name="total"]').val())
        })
        $('#transaction-form').submit(function(e) {
            e.preventDefault()
            $('#transaction-save-btn').attr('disabled', true)
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
            _el.addClass('pop_msg')
            $.ajax({
                url: './Actions.php?a=save_transaction',
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error: err => {
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("An error occurred.")
                    _this.prepend(_el)
                    _el.show('slow')
                    $('#transaction-save-btn').attr('disabled', false)
                },
                success: function(resp) {
                    if (resp.status == 'success') {
                        setTimeout(() => {
                            uni_modal("RECEIPT", "view_receipt.php?id=" + resp.transaction_id)
                        }, 1000);
                    } else {
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                    $('#transaction-save-btn').attr('disabled', false)
                }
            })
        })
        $('#transaction-form input').keydown(function(e) {
            if (e.which == 13) {
                e.preventDefault()
                return false
            }
        })
    })

    function compute(_this, gst) {
        _this.find('[name="quantity[]"]').on('input keydown', function() {
            var qty = $(this).val() > 0 ? $(this).val() : 0;
            var price = _this.find('[name="price[]"]').val()
            var main = parseFloat(((parseFloat(price) + ((parseFloat(price) * parseFloat(gst)) / 100)) * qty)).toFixed(2);
            var _total = parseFloat(qty) * parseFloat(price)

            _this.find('.total').text(parseFloat(_total).toLocaleString('en-US', {
                style: 'decimal',
                maximumFractionDigits: 2
            }))
            _this.find('.main').text(parseFloat(main).toLocaleString('en-US', {
                style: 'decimal',
                maximumFractionDigits: 2
            }))
            calculate_total()
        })
        _this.find('[name="quantity[]"]').on('focusout', function() {
            if ($(this).val() <= 0) {
                $(this).val('0')
            }
        })
        _this.on('focusin', function() {
            $(this).addClass("bg-light bg-opacity-50 selected-item")
            $('#remove-item').attr('disabled', false)
        })
        _this.on('focusout', function() {
            if ($('#remove-item').is(':focus') == true || $('#remove-item').is(':hover') == true)
                return false;
            $(this).removeClass("bg-primary bg-opacity-50 selected-item")
            $('#remove-item').attr('disabled', true)
        })
        $('#transaction-form input').keydown(function(e) {
            if (e.which == 13) {
                e.preventDefault()
                return false
            }
        })
    }

    function calculate_total() {
        var sub = 0
        var total = 0
        var discount = 0
        $('#item-list tr #main').each(function() {
            val = $(this).text()
            sub += parseFloat(val)
            console.log(val);
        })
        sub = sub.toFixed(3);
        $('#subTotal').text(parseFloat(sub))
        discount = sub * .12;
        $('#tax').text(parseFloat(discount).toLocaleString('en-US', {
            style: 'decimal',
            manimumFractionDigits: 2,
            maximumFractionDigits: 2
        }))

        $('#total').text(parseFloat(sub)) //total issue fixed;
        $('[name="total"]').val(parseFloat(sub)) //save transaction-sav-btn issue fixed
        $('[name="sub_total"]').val(parseFloat(parseFloat(sub)))

    }

    function remove_item() {
        $('#item-list tr.selected-item').remove()
        calculate_total()
        $('#remove-item').attr('disabled', true)
    }
</script>