<div class="pencipta">
    <div class="form-group">
        <label>NIK:</label>
        <input type="number" name="nik[]" spellcheck="false" class="required-field">
    </div>

    <div class="form-group">
        <label>Nama:</label>
        <input type="text" name="nama[]" spellcheck="false" class="required-field">
    </div>

    <div class="form-group">
        <label>No. Telepon:</label>
        <input type="tel" name="no_telepon[]" spellcheck="false" class="required-field">
    </div>

    <div class="form-group">
        <label>Jenis Kelamin:</label>
        <select name="jenis_kelamin[]" id="jenis_kelamin" class="required-field">
            <option value="">-- Pilih Jenis Kelamin --</option>
            <option value="Laki-laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>
        </select>
    </div>

    <div class="form-group">
        <label>Alamat:</label>
        <textarea name="alamat[]" spellcheck="false" class="required-field"></textarea>
    </div>

    <div class="form-group">
        <label>Negara:</label>
        <select name="negara[]" id="nationalityform" class="required-field">
            <option value="">-- Pilih Negara --</option>
        </select>
    </div>

    <!-- Form Indonesia -->
    <div id="indonesia-form" style="display: none;">
        <div class="form-group">
            <label>Provinsi:</label>
            <select name="provinsi[]" class="provinsi required-field auto-search">
                <option value="">-- Pilih Provinsi --</option>
            </select>
        </div>

        <div class="form-group">
            <label>Kabupaten/Kota:</label>
            <select name="kota[]" class="kabupaten required-field auto-search">
                <option value="">-- Pilih Kabupaten/Kota --</option>
            </select>
        </div>

        <div class="form-group">
            <label>Kecamatan:</label>
            <select name="kecamatan[]" class="kecamatan required-field auto-search">
                <option value="">-- Pilih Kecamatan --</option>
            </select>
        </div>

        <div class="form-group">
            <label>Kelurahan:</label>
            <select name="kelurahan[]" class="kelurahan required-field auto-search">
                <option value="">-- Pilih Kelurahan --</option>
            </select>
        </div>

        <div class="form-group">
            <label>Kode Pos:</label>
            <input type="text" class="kodepos" name="kode_pos[]" placeholder="Kode Pos" readonly />
        </div>
    </div>

    <!-- Form Luar Indonesia -->
    <div id="non-indonesia-form" style="display: none;">
        <div class="form-group">
            <label>State:</label>
            <select name="provinsi[]" class="state required-field auto-search"></select>
        </div>

        <div class="form-group">
            <label>City:</label>
            <select name="kota[]" class="city required-field auto-search"></select>
        </div>

        <div class="form-group">
            <label>District:</label>
            <input type="text" name="kecamatan[]" class="manual-kecamatan required-field" placeholder="Manual Input" />
        </div>

        <div class="form-group">
            <label>Village:</label>
            <input type="text" name="kelurahan[]" class="manual-kelurahan required-field" placeholder="Manual Input"/>
        </div>

        <div class="form-group">
            <label>Postal Code:</label>
            <input type="text" name="kode_pos[]" class="manual-kodepos required-field" placeholder="Manual Input"/>
        </div>
    </div>