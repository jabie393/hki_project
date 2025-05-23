<div class="pencipta">
    <div class="form-group">
        <label>NIK:</label>
        <input type="number" name="nik[]" spellcheck="false" required>
    </div>

    <div class="form-group">
        <label>Nama:</label>
        <input type="text" name="nama[]" spellcheck="false" required>
    </div>

    <div class="form-group">
        <label>No. Telepon:</label>
        <input type="tel" name="no_telepon[]" spellcheck="false" required>
    </div>

    <div class="form-group">
        <label>Jenis Kelamin:</label>
        <select name="jenis_kelamin[]" required>
            <option value="Laki-laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>
        </select>
    </div>

    <div class="form-group">
        <label>Alamat:</label>
        <textarea name="alamat[]" spellcheck="false" required></textarea>
    </div>

    <div class="form-group">
        <label>Negara:</label>
        <select name="negara[]" id="nationalityform" required>
            <option value="">-- Pilih Negara --</option>
        </select>
    </div>

    <!-- Form Indonesia -->
    <div id="indonesia-form" style="display: none;">
        <div class="form-group">
            <label>Provinsi:</label>
            <select name="provinsi[]" class="provinsi"></select>
        </div>

        <div class="form-group">
            <label>Kabupaten/Kota:</label>
            <select name="kota[]" class="kabupaten"></select>
        </div>

        <div class="form-group">
            <label>Kecamatan:</label>
            <select name="kecamatan[]" class="kecamatan"></select>
        </div>

        <div class="form-group">
            <label>Kelurahan:</label>
            <select name="kelurahan[]" class="kelurahan"></select>
        </div>

        <div class="form-group">
            <label>Kode Pos:</label>
            <input type="text" class="kodepos" name="kode_pos[]" />
        </div>
    </div>

    <!-- Form Luar Indonesia -->
    <div id="non-indonesia-form" style="display: none;">
        <div class="form-group">
            <label>State:</label>
            <select name="provinsi[]" class="state"></select>
        </div>

        <div class="form-group">
            <label>City:</label>
            <select name="kota[]" class="city"></select>
        </div>

        <div class="form-group">
        <label>District (manual):</label>
        <input type="text" name="kecamatan[]" class="manual-kecamatan" />
        </div>

        <div class="form-group">
        <label>Village (manual):</label>
        <input type="text" name="kelurahan[]" class="manual-kelurahan" />
        </div>

        <div class="form-group">
        <label>Zip Code (manual):</label>
        <input type="text" name="kode_pos[]" class="manual-kodepos" />
        </div>
    </div>