
    
        const banks = [
            {name: "Ngân hàng TMCP An Bình", shortCode: "ABB", bin: "970425"},
            {name: "Ngân hàng TMCP Á Châu", shortCode: "ACB", bin: "970416"},
            {name: "Ngân hàng TMCP Bắc Á", shortCode: "BAB", bin: "970409"},
            {name: "Ngân hàng TMCP Đầu tư và Phát triển Việt Nam", shortCode: "BIDV", bin: "970418"},
            {name: "Ngân hàng TMCP Bảo Việt", shortCode: "BVB", bin: "970438"},
            {name: "CAKE by VPBank (số)", shortCode: "CAKE", bin: "546034"},
            {name: "Ngân hàng Xây dựng Việt Nam (CBB)", shortCode: "CBB", bin: "970444"},
            {name: "CIMB Việt Nam", shortCode: "CIMB", bin: "422589"},
            {name: "Citibank", shortCode: "CITIBANK", bin: "533948"},
            {name: "Ngân hàng Hợp tác xã Việt Nam", shortCode: "COOPBANK", bin: "970446"},
            {name: "DBS Bank (HCM)", shortCode: "DBS", bin: "796500"},
            {name: "Ngân hàng TMCP Đông Á", shortCode: "DOB", bin: "970406"},
            {name: "Ngân hàng TMCP Xuất Nhập khẩu Việt Nam", shortCode: "EIB", bin: "970431"},
            {name: "GPB Bank", shortCode: "GPB", bin: "970408"},
            {name: "Ngân hàng TMCP Phát triển TP Hồ Chí Minh", shortCode: "HDB", bin: "970437"},
            {name: "Hong Leong Việt Nam", shortCode: "HLBVN", bin: "970442"},
            {name: "HSBC Việt Nam", shortCode: "HSBC", bin: "458761"},
            {name: "Ngân hàng Công nghiệp Hàn Quốc - HCM", shortCode: "IBK-HCM", bin: "970456"},
            {name: "Ngân hàng Công nghiệp Hàn Quốc - Hà Nội", shortCode: "IBK-HN", bin: "970455"},
            {name: "Ngân hàng TMCP Công thương Việt Nam", shortCode: "ICB", bin: "970415"},
            {name: "Indovina Bank", shortCode: "IVB", bin: "970434"},
            {name: "Kookmin HCM", shortCode: "KBHCM", bin: "970463"},
            {name: "Kookmin Hà Nội", shortCode: "KBHN", bin: "970462"},
            {name: "Kasikornbank", shortCode: "KBank", bin: "668888"},
            {name: "KEB Hana HCM", shortCode: "KEBHANAHCM", bin: "970466"},
            {name: "KEB Hana Hà Nội", shortCode: "KEBHANAHN", bin: "970467"},
            {name: "Ngân hàng TMCP Kiên Long", shortCode: "KLB", bin: "970452"},
            {name: "Liên Việt PostBank", shortCode: "LPBANK", bin: "970449"},
            {name: "MB Bank", shortCode: "MB", bin: "970422"},
            {name: "Maritime Bank", shortCode: "MSB", bin: "970426"},
            {name: "Nam Á Bank", shortCode: "NAB", bin: "970428"},
            {name: "NCB", shortCode: "NCB", bin: "970419"},
            {name: "NH Nonghyup", shortCode: "NHB HN", bin: "801011"},
            {name: "OCB", shortCode: "OCB", bin: "970448"},
            {name: "OceanBank", shortCode: "OCEANBANK", bin: "970414"},
            {name: "Public Bank", shortCode: "PBVN", bin: "970439"},
            {name: "Petrolimex Bank", shortCode: "PGB", bin: "970430"},
            {name: "PVCB", shortCode: "PVCB", bin: "970412"},
            {name: "Saigon Commercial Bank (SCB)", shortCode: "SCB", bin: "970429"},
            {name: "Standard Chartered Vietnam", shortCode: "SCVN", bin: "970410"},
            {name: "SeABank", shortCode: "SEAB", bin: "970440"},
            {name: "SGICB", shortCode: "SGICB", bin: "970400"},
            {name: "SHB", shortCode: "SHB", bin: "970443"},
            {name: "Shinhan Vietnam", shortCode: "SHBVN", bin: "970424"},
            {name: "Sacombank", shortCode: "STB", bin: "970403"},
            {name: "Techcombank", shortCode: "TCB", bin: "970407"},
            {name: "Timo by Ban Viet Bank", shortCode: "TIMO", bin: "963388"},
            {name: "TPBank", shortCode: "TPB", bin: "970423"},
            {name: "UOB HCM", shortCode: "UOB", bin: "970458"},
            {name: "Ubank by VPBank", shortCode: "Ubank", bin: "546035"},
            {name: "VietABank", shortCode: "VAB", bin: "970427"},
            {name: "Agribank", shortCode: "VBA", bin: "970405"},
            {name: "VBSP (Ngân hàng Chính sách Xã hội)", shortCode: "VBSP", bin: "999888"},
            {name: "Vietcombank", shortCode: "VCB", bin: "970436"},
            {name: "VCCB", shortCode: "VCCB", bin: "970454"},
            {name: "VIB", shortCode: "VIB", bin: "970441"},
            {name: "Vietbank", shortCode: "VIETBANK", bin: "970433"},
            {name: "VietinBank", shortCode: "VPB", bin: "970432"},
            {name: "VPBank", shortCode: "VPB", bin: "970432"},
            {name: "VRB", shortCode: "VRB", bin: "970421"},
            {name: "Woori Vietnam", shortCode: "WVN", bin: "970457"},
            {name: "Ví MoMo", shortCode: "MOMO", bin: "momo"}
        ];

        const bankDisplay = document.getElementById('bank_display');
        const bankBin = document.getElementById('bank_bin');
        const bankDropdown = document.querySelector('.bank-dropdown');
        const bankSearch = document.getElementById('bank_search');
        const bankList = document.getElementById('bank_list');
        const searchIcon = document.querySelector('.bank-search-icon');


        function renderBankList(banksToShow = banks) {
            bankList.innerHTML = '';
            
            if (banksToShow.length === 0) {
                bankList.innerHTML = '<div class="no-results">Không tìm thấy ngân hàng nào</div>';
                return;
            }
            
            banksToShow.forEach(bank => {
                const bankItem = document.createElement('div');
                bankItem.className = 'bank-item';
                bankItem.innerHTML = `
                    <div class="bank-name">${bank.name}</div>
                    <div class="bank-info">Mã: ${bank.shortCode} | BIN: ${bank.bin}</div>
                `;
                bankItem.addEventListener('click', () => selectBank(bank));
                bankList.appendChild(bankItem);
            });
        }

        function selectBank(bank) {
            bankDisplay.value = `${bank.name} (${bank.shortCode})`;
            bankBin.value = bank.bin;
            closeBankDropdown();
        }

    
        function searchBanks(query) {
            const searchTerm = query.toLowerCase();
            const filtered = banks.filter(bank => 
                bank.name.toLowerCase().includes(searchTerm) || 
                bank.shortCode.toLowerCase().includes(searchTerm)
            );
            renderBankList(filtered);
        }

        function openBankDropdown() {
            bankDropdown.classList.add('show');
            searchIcon.classList.add('rotated');
            bankSearch.focus();
        }


        function closeBankDropdown() {
            bankDropdown.classList.remove('show');
            searchIcon.classList.remove('rotated');
            bankSearch.value = '';  
            renderBankList();
        }


        bankDisplay.addEventListener('click', openBankDropdown);

        bankSearch.addEventListener('input', (e) => {
            searchBanks(e.target.value);
        });


        document.addEventListener('click', (e) => {
            if (!document.querySelector('.bank-search-container').contains(e.target)) {
                closeBankDropdown();
            }
        });

  
        renderBankList();

        if (bankBin.value) {
            const currentBank = banks.find(bank => bank.bin === bankBin.value);
            if (currentBank) {
                bankDisplay.value = `${currentBank.name} (${currentBank.shortCode})`;
            }
        }
  