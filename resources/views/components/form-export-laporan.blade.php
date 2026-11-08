<!-- Button trigger modal -->
<button type="button" class="btn btn-round btn-dark" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
  Export
</button>

<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('export-laporan-transaksi') }}" method="POST">
        @csrf
        <input type="hidden" name="jenis_transaksi" value="{{ $jenisTransaksi }}">
        <div class="modal-header">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Form Export Laporan</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        @if($jenisTransaksi == 'pemasukan')
        <div class="form-group">
            <label for="pengirim" class="form-label">Pengirim</label>
            <input type="text" class="form-control" id="pengirim" name="pengirim" placeholder="Masukkan nama pengirim">
        </div>
        @else
        <div class="form-group">
            <label for="penerima" class="form-label">Penerima</label>
            <input type="text" class="form-control" id="penerima" name="penerima" placeholder="Masukkan nama penerima">
        </div>
        @endif
        <div class="form-group">
            <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
            <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal">
            @error('tanggal_awal')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
            <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir">
            @error('tanggal_akhir')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group mt-1 d-flex align-items-center gap-1">
            <label for="is_completed" class="form-label">
                Download Dengan Items
                <input type="checkbox" id="is_completed" name="is_completed">
            </label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Export Laporan</button>
      </div>
      </form>
    </div>
  </div>
</div>