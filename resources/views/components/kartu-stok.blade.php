@props(['nomorSku' => null])

<div>
<!-- Button trigger modal -->
<button type="button" class="btn btn-default btn-kartu-stok text-primary" data-bs-toggle="modal" 
data-bs-target="#kartuStokModal" data-nomor-sku="{{ $nomorSku }}">
  Kartu Stok
</button>

<!-- Modal -->
<div class="modal fade" id="kartuStokModal" tabindex="-1" aria-labelledby="kartuStokModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Kartu Stok</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table" id="table-kartu-stok">
          <thead>
            <tr>
                <th scope="col">No</th>
                <th scope="col">Tanggal</th>
                <th scope="col">Nomor Transaksi</th>
                <th scope="col">Note</th>
                <th scope="col">Jumlah Masuk</th>
                <th scope="col">Jumlah Keluar</th>
                <th scope="col">Stok Akhir</th>
                <th scope="col">Petugas</th>
            </tr>
          </thead>
          <tbody id="kartu-stok-body">
            <!-- Data will be populated here via JavaScript -->
          </tbody>

        </table>
        <div id="pagination-kartu-stok" class="mt-3">

        </div>
      </div>
    </div>
  </div>
</div>
</div>

@push('scripts')
<script>
    $(document).ready(function(){
    let currentNomorSku = null;

    function transaksiColor(value){
        switch(value){
            case 'in':
                return 'bg-success';
            case 'out':
                return 'bg-danger';
            case 'adjustment':
                return 'bg-secondary';
            default:
                return 'bg-warning';
        }
    }

    function loadKartuStok(nomorSku, pageUrl = null) {
    const url = pageUrl || `/kartu-stok/${nomorSku}`;
    const $tbody = $('#table-kartu-stok tbody');
    const $pagination = $('#pagination-kartu-stok');

    $tbody.html('<tr><td colspan="8" class="text-center">Loading...</td></tr>');

    $.ajax({
        type: 'GET',
        url: url,
        success: function(response) {
            $tbody.empty();
            console.log(response);
            const logger = response.data;

            if (logger.length === 0) {
                $tbody.html('<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>');
            }
            
            logger.forEach((item, index)=>{
                $tbody.append(`
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.tanggal}</td>
                        <td>${item.nomor_transaksi ?? '-'}</td>
                        <td>
                            <span class="badge ${transaksiColor(item.jenis_transaksi)}" fw-bold text-uppercase>
                                ${item.jenis_transaksi}
                            </span>
                        </td>
                        <td>${item.jumlah_masuk ?? '-'}</td>
                        <td>${item.jumlah_keluar ?? '-'}</td>
                        <td>${item.stok_akhir}</td>
                        <td>${item.petugas}</td>
                    </tr>
                `);
            })

            if(response.meta.total > response.meta.per_page){
                const meta = response.meta;
                let paginationHtml = '<nav><ul class="pagination justify-content-center gap-1">';
                meta.links.forEach(link => {
                    const isNumber = /^\d+$/.test(link.label);
                    if(!isNumber) return;
                    paginationHtml += `
                    <li class="page-item ${link.active ? 'active' : ''}">
                        <a class="page-link" href="${link.url}" data-page="${link.label}">${link.label}</a>
                    </li>`;

                    
                });
                paginationHtml += '</ul></nav>';
                    $pagination.html(paginationHtml);
            }
        }
    });
}

        $(document).on('click', '.btn-kartu-stok', function() {
            const currentNomorSku = $(this).data('nomor-sku');
            loadKartuStok(currentNomorSku);
        });

        $(document).on('click', '#pagination-kartu-stok .page-link', function(e) {
            e.preventDefault();
            const pageUrl = $(this).attr('href');
            if (pageUrl && currentNomorSku) {
                loadKartuStok(currentNomorSku, pageUrl);
            }
        });
    });
</script>
@endpush