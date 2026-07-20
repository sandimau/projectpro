<?php

function grouping($obj)
{
    $hasil = array();
    $tampilan = '';

    $order_id = 0;
    $tampilan = '';
    foreach ($obj as $detail) {

        /////// tambah baris baru

        if ($order_id != $detail->order_id) {
            if ($order_id != 0) {
                $tampilan .= '<div class=pull-right></div></a>';
            }

            $warna = '';
            $nominal = '';
            $order = $detail->order;

            $total = $order->total;
            if ($total < 1000000) {
                $warna = 'black';
                if ($total == 0) {
                    $nominal = 0;
                } else {
                    $nominal = floor($total / 1000) . 'rb';
                }

            } else {

                if ($total <= 5000000) {
                    $warna = 'green';
                } else if ($total <= 10000000) {
                    $warna = '#FAA814';
                } else {
                    $warna = '#D93007';
                }

                $nominal = round($total, -5) / 1000000 . 'jt';
            }

            $konsumen = $order->kontak;

            $model_ar = $konsumen->ar->kode ?? 'kosong';
            $tampilan .= "<a class='popup d-flex'  href='" . url('admin/order/' . $detail->order_id . '/detail') . "' >
            <p style='font-weight:600' class='text-default'>";

            $tampilan .= " <span class='label label-rounded' style='background-color: " . $konsumen->ar->warna . "'> " . $konsumen->ar->kode . "  </span>";

            $tampilan .= " <span class='label label-rounded mr-1' style='background-color: " . $warna . "'> " . $nominal . "  </span> ";

            $tampilan .= $konsumen->nama . '</p>';

        }

        ////////////////ngisi order detail

        $proses = '';
        if (!empty($detail->process)) {
            $proses = "<span class='label label-info  label-rounded' style='background-color: " . '#' . $detail->process->warna . ";'>" . $detail->process->nama . "</span>";
        }

        $nama_produk = $detail->produk->nama;

        $jadwalx = '';

        $jadwal = $detail->jadwal()->find($detail->order_flow_id);

        if ($jadwal) {
            $deadline = $jadwal->pivot->deadline;
            // $tampilan.= newJadwal($deadline);
            $jadwalx = newJadwal($deadline);
            // " <span class='label label-info ' style='background-color:red'>".$deadline."</span>";
        }

        $tampilan .= "<span style='color:#636363; padding-right:5px;'> " . $nama_produk . " " . $proses . $jadwalx . "</span> ";

        $order_id = $detail->order_id;
    }

    if ($order_id != 0) {
        $tampilan .= '
        <div class=pull-right></div>
        </a>';
    }

    return $tampilan;
}

function newJadwal($deadline)
{
    if (!empty($deadline)) {
        $date1 = date_create(now());
        $date2 = date_create($deadline);
        $diff = date_diff($date1, $date2);
        $days = $diff->days;

        $hasil = $days;
        if ($days == 0) {
            $class = "warning";
        } else if ($days > 0) {
            if ($diff->invert == 1) {
                $hasil = "-" . $hasil;
                $class = "danger";
            } else {
                $class = "success";
            }

        }
        return " <small> <span class='label label-" . $class . "' style='font-size:90%'>" . $hasil . "</span></small>";
    } else {
        return "";
    }
}
