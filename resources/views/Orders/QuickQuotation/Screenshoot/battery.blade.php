<style>
    #body-screenshoot {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: #f0f0f0;
    }

    .containerx {
        background-color: #333;
        color: white;
        padding: 20px;
        border-radius: 10px;
    }

    table {
        /* width: 100%; */
        border-collapse: collapse;
    }

    th,
    td {
        padding: 10px;
        text-align: center;
        border: 1px solid #444;
    }

    th {
        background-color: #555;
    }

    td {
        background-color: #666;
    }

    .unavailable {
        background-color: #999;
        color: #000;
    }

    .item img {
        max-width: 100px;
        height: auto;
    }

    .item s {
        /* color: #ff0000; */
    }

    .item strong {
        color: #00ff00;
    }

    .img-responsive {
        width: 100%;
        height: auto;
    }
</style>
</head>

<!-- button screenshoot -->

<div id="spinner" class="spinner-border text-primary m-2" role="status"></div>
<div id="body-screenshoot" style="display: none;">
    <div class="containerx" id="div-screenshoot">
        <table>
            <thead>
                <tr>
                    <th style="width: 150px;">KATEGORI</th>
                    <?php foreach ($batteries['categories'] as $category) : ?>
                        <th style="background-color:#aadf2d;color:#000;width: 150px;"><?= $category ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($batteries as $key => $values) : ?>
                    <?php if ($key == 'categories') {
                        continue;
                    } ?>
                    <tr>
                        <th><?= $values[0] ?></th>
                        <?php for ($i = 1; $i < count($values); $i++) : ?>
                            <?php if ($key == 'prices' && !empty($values[$i])) :
                                if (empty($values[$i]['discount'])) : ?>
                                    <td class="item" style="background-color: white;">
                                        <strong style="color:#010202;"><?= $values[$i]['netto'] ?></strong>
                                    </td>
                                <?php else : ?>
                                    <td class="item" style="background-color: white;">
                                        <s style="color:#89837c;"><?= $values[$i]['original'] ?></s><br>
                                        <b style="background-color: #ff0000; display: block;"> Disc
                                            <?= $values[$i]['discount'] ?>%</b>
                                        <strong style="color:#010202;">Netto: <?= $values[$i]['netto'] ?></strong>
                                    </td>
                                <?php endif; ?>
                            <?php elseif ($key == 'images' && !empty($values[$i])) : ?>
                                <td class="item" style="background-color: white;"><img src="<?= $values[$i] ?>" alt="Battery <?= $i ?>"></td>
                            <?php elseif (empty($values[$i])) : ?>
                                <td class="item" style="background-color: white; color:#000;">
                                    <?= $key == 'units' ? '#TIDAK TERSEDIA' : '' ?></td>
                            <?php else : ?>
                                <td class="item" style="background-color: white; color:#000;"><b><?= $values[$i] ?></b></td>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<img src="" alt="" id="image-coppy" class="img-responsive">

<script src="{{ asset('/js/html2canvas.min.js') }}"></script>

<script>
    document.getElementById('screenshoot-btn').addEventListener('click', function handleClick() {
        // sleep 5 seconds after click and show body-screenshoot
        // to make sure all content is loaded
        // add loading spinner
        setTimeout(() => {
            document.getElementById('spinner').style.display = 'none';
            document.getElementById('screenshoot-btn').style.display = 'none';
            screenShoot();
        }, 3000);
    });

    function screenShoot() {
        // show body-screenshoot
        document.getElementById('body-screenshoot').style.display = 'flex';

        // hide tag img
        document.getElementById('image-coppy').src = '';
        html2canvas(document.querySelector("#div-screenshoot")).then(canvas => {
            var link = document.createElement('a');
            link.download = 'battery.png';
            link.href = canvas.toDataURL('image/png');
            // link.click();

            // hide body-screenshoot
            document.getElementById('body-screenshoot').style.display = 'none';

            // set image to img tag
            document.getElementById('image-coppy').src = canvas.toDataURL('image/png');

            // Copy to clipboard from img tag
            document.getElementById('image-coppy').addEventListener('click', function() {
                navigator.clipboard.write([
                    new ClipboardItem({
                        'image/png': canvas.toDataURL('image/png')
                    })
                ]).then(function() {
                    console.log('Image copied to clipboard.');
                }).catch(function(err) {
                    console.error('Unable to copy to clipboard: ', err);
                });
            });
        });
    }


    // when body screenshoot complete load, trigger click event
    window.addEventListener('load', function() {
        alert('Load');
        // Setelah seluruh konten termasuk gambar selesai di-load, trigger click
        document.getElementById('screenshoot-btn').click();
    });
</script>