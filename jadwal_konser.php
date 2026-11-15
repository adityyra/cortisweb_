<?php
require_once __DIR__ . '/db_connect.php';

// Fetch concerts from database
$concerts = [];
if (isset($conn) && $conn && !$conn->connect_error) {
    $res = $conn->query("SELECT * FROM `concert_schedules` ORDER BY `concert_date` ASC");
    if ($res) {
        while ($r = $res->fetch_assoc()) {
            $concerts[] = $r;
        }
    }
}

// Fallback dummy data if DB empty
if (empty($concerts)) {
    $concerts = [
        ['id' => 1, 'tour_title' => 'World Tour: "The First Chapter"', 'concert_date' => '2026-07-10', 'location' => 'SEOUL, KOREA', 'venue' => 'OLYMPIC HALL', 'ticket_status' => 'SOLD OUT'],
        ['id' => 2, 'tour_title' => 'World Tour: "The First Chapter"', 'concert_date' => '2026-07-11', 'location' => 'SEOUL, KOREA', 'venue' => 'OLYMPIC HALL', 'ticket_status' => 'SOLD OUT'],
        ['id' => 3, 'tour_title' => 'World Tour: "The First Chapter"', 'concert_date' => '2026-08-21', 'location' => 'TOKYO, JAPAN', 'venue' => 'TOKYO DOME', 'ticket_status' => 'AVAILABLE'],
        ['id' => 4, 'tour_title' => 'World Tour: "The First Chapter"', 'concert_date' => '2026-08-31', 'location' => 'JAKARTA, INDONESIA', 'venue' => 'ICE BSD CITY', 'ticket_status' => 'AVAILABLE'],
        ['id' => 5, 'tour_title' => 'World Tour: "The First Chapter"', 'concert_date' => '2026-09-15', 'location' => 'BANGKOK, THAILAND', 'venue' => 'IMPACT ARENA', 'ticket_status' => 'AVAILABLE'],
    ];
}

// Filter available concerts for booking modal
$availableConcerts = array_filter($concerts, function($c) {
    return $c['ticket_status'] === 'AVAILABLE';
});
$today = new DateTime();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concert Tour - CORTIS Official</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Syne:wght@700;800&family=Outfit:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="jadwal_konser.css">
</head>

<body>

    <div class="wireframe-container">

        <!-- HEADER -->
        <header>
            <div class="logo-container">
                <a href="index.php" class="profile-logo" style="text-decoration: none;">
                    <img src="images/CORTISLOGO.png" alt="Cortis Logo">
                </a>
                <a href="index.php" class="logo-brand">CORTIS</a>
            </div>
            <nav class="nav-menu" id="navMenu">
                <a href="index.php">HOME</a>
                <a href="about.php">ABOUT</a>
                <div class="dropdown">
                    <a href="music.php">MUSIC</a>
                    <div class="dropdown-content">
                        <a href="videos.php">VIDEOS</a>
                    </div>
                </div>
                <a href="jadwal_konser.php" class="active">TOUR</a>
                <a href="galeri.php">GALLERY</a>
                <a href="notice.php">NOTICE</a>
            </nav>
            <button class="menu-toggle" id="menuToggle" aria-label="Toggle Menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
        </header>

        <!-- SIDE INDICATOR -->
        <div class="side-indicator">
            <div class="side-number">05</div>
            <div class="side-line"></div>
            <div class="side-text">CONCERT</div>
        </div>

        <main>

            <!-- CONCERT SCHEDULE SECTION -->
            <section class="concert-section">
                <h2 class="section-title">World Tour: "The First Chapter"</h2>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>DATE</th>
                                <th>LOCATION</th>
                                <th>VENUE</th>
                                <th>STATUS</th>
                                <th>COUNTDOWN CLOCK</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($concerts as $c): 
                                $cDate = new DateTime($c['concert_date']);
                                $diff = $today->diff($cDate);
                                $isFuture = ($cDate >= $today);
                                $daysLeft = $diff->days;
                                $statusUpper = strtoupper($c['ticket_status']);
                                $isAvailable = ($statusUpper === 'AVAILABLE');
                                $formattedDate = strtoupper($cDate->format('M d'));
                            ?>
                                <tr>
                                    <td><?php echo $formattedDate; ?></td>
                                    <td><?php echo htmlspecialchars($c['location']); ?></td>
                                    <td><?php echo htmlspecialchars($c['venue']); ?></td>
                                    <td class="<?php echo $isAvailable ? 'status-available' : 'status-soldout'; ?>">
                                        <?php echo $statusUpper; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if (!$isFuture) {
                                            echo '-';
                                        } elseif ($isAvailable) {
                                            echo "[ {$daysLeft} More Days ]";
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($isAvailable): ?>
                                            <button type="button" onclick="openTicketModal(<?php echo $c['id']; ?>, '<?php echo addslashes($c['location']); ?>', '<?php echo addslashes($c['venue']); ?>', '<?php echo $c['concert_date']; ?>')">BUY TICKET</button>
                                        <?php else: ?>
                                            <button disabled>SOLD OUT</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- ======================================================= -->
            <!-- TICKET PURCHASE MODAL (Tabel: ticket_orders)            -->
            <!-- ======================================================= -->
            <div id="ticketModal" class="modal-overlay" style="display: none;">
                <div class="modal-box">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <div>
                            <span class="modal-badge">OFFICIAL TICKETING</span>
                            <h2 class="modal-title">BUY CONCERT TICKET</h2>
                            <p class="modal-subtitle">World Tour: "The First Chapter"</p>
                        </div>
                        <button type="button" class="modal-close" onclick="closeTicketModal()" aria-label="Close modal">&times;</button>
                    </div>

                    <!-- 1. Ticket Order Form (English) -->
                    <form id="ticketOrderForm" onsubmit="handleTicketOrder(event)">
                        <!-- Selected Concert Information Banner -->
                        <div class="concert-selected-card">
                            <div class="concert-info-item">
                                <span class="label">LOCATION</span>
                                <strong id="displayConcertLocation">JAKARTA, INDONESIA</strong>
                            </div>
                            <div class="concert-info-item">
                                <span class="label">VENUE</span>
                                <strong id="displayConcertVenue">ICE BSD CITY</strong>
                            </div>
                            <div class="concert-info-item">
                                <span class="label">DATE</span>
                                <strong id="displayConcertDate">2026-08-31</strong>
                            </div>
                        </div>

                        <!-- Form Fields Grid (Matching ticket_orders Table Specification) -->
                        <div class="form-grid">
                            <!-- concert_id: Foreign Key referencing concert_schedules.id -->
                            <div class="form-group full-width">
                                <label for="concert_id">SELECT CONCERT SCHEDULE <span class="req">*</span></label>
                                <select id="concert_id" name="concert_id" required onchange="onConcertChange(this.value)">
                                    <?php foreach ($availableConcerts as $idx => $ac): 
                                        $d = new DateTime($ac['concert_date']);
                                        $dispDate = strtoupper($d->format('M d, Y'));
                                    ?>
                                        <option value="<?php echo $ac['id']; ?>" data-location="<?php echo htmlspecialchars($ac['location']); ?>" data-venue="<?php echo htmlspecialchars($ac['venue']); ?>" data-date="<?php echo $ac['concert_date']; ?>">
                                            <?php echo htmlspecialchars($ac['location']); ?> — <?php echo htmlspecialchars($ac['venue']); ?> (<?php echo $dispDate; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- fullname: VARCHAR(100) NOT NULL -->
                            <div class="form-group">
                                <label for="fullname">FULL NAME (AS ON ID / PASSPORT) <span class="req">*</span></label>
                                <input type="text" id="fullname" name="fullname" placeholder="e.g. John Doe" required maxlength="100">
                                <small>Must match your government-issued ID card</small>
                            </div>

                            <!-- identity_no: VARCHAR(16) NOT NULL -->
                            <div class="form-group">
                                <label for="identity_no">IDENTITY NUMBER (16-DIGIT NIK / PASSPORT) <span class="req">*</span></label>
                                <input type="text" id="identity_no" name="identity_no" placeholder="e.g. 6171012304000001" required maxlength="16" pattern="[0-9]{16}" title="Must be 16-digit ID / Passport number">
                                <small>16-digit NIK or passport number for verification</small>
                            </div>

                            <!-- email: VARCHAR(100) NOT NULL -->
                            <div class="form-group">
                                <label for="email">EMAIL ADDRESS <span class="req">*</span></label>
                                <input type="email" id="email" name="email" placeholder="e.g. johndoe@example.com" required maxlength="100">
                                <small>Official invoice & E-Ticket PDF will be sent here</small>
                            </div>

                            <!-- phone: VARCHAR(20) NOT NULL -->
                            <div class="form-group">
                                <label for="phone">WHATSAPP / PHONE NUMBER <span class="req">*</span></label>
                                <input type="tel" id="phone" name="phone" placeholder="e.g. 081234567890" required maxlength="20">
                                <small>Active contact number for ticket entry gates</small>
                            </div>

                            <!-- ticket_category: VARCHAR(50) NOT NULL -->
                            <div class="form-group">
                                <label for="ticket_category">TICKET CATEGORY <span class="req">*</span></label>
                                <select id="ticket_category" name="ticket_category" required onchange="calculateOrderTotal()">
                                    <option value="VIP" data-price="2750000">VIP (Soundcheck + Laminate) — Rp 2,750,000</option>
                                    <option value="Festival" data-price="1850000" selected>Festival (Standing Area) — Rp 1,850,000</option>
                                    <option value="Tribune" data-price="1250000">Tribune (Numbered Seating) — Rp 1,250,000</option>
                                </select>
                            </div>

                            <!-- quantity: INT NOT NULL, DEFAULT 1 (Max 4) -->
                            <div class="form-group">
                                <label for="quantity">TICKET QUANTITY (MAX 4) <span class="req">*</span></label>
                                <div class="qty-wrapper">
                                    <button type="button" class="btn-qty" onclick="changeQty(-1)">-</button>
                                    <input type="number" id="quantity" name="quantity" min="1" max="4" value="1" readonly required>
                                    <button type="button" class="btn-qty" onclick="changeQty(1)">+</button>
                                </div>
                                <small>Maximum 4 tickets allowed per transaction</small>
                            </div>

                            <!-- payment_method: VARCHAR(50) NOT NULL -->
                            <div class="form-group full-width">
                                <label for="payment_method">PAYMENT METHOD <span class="req">*</span></label>
                                <select id="payment_method" name="payment_method" required>
                                    <option value="Bank Transfer - BCA">Bank Transfer - BCA Virtual Account</option>
                                    <option value="Bank Transfer - Mandiri">Bank Transfer - Mandiri Virtual Account</option>
                                    <option value="Bank Transfer - BNI">Bank Transfer - BNI Virtual Account</option>
                                    <option value="GoPay / QRIS">E-Wallet (GoPay / QRIS Instant)</option>
                                    <option value="Credit Card / Debit">Credit Card / Debit (Visa / Mastercard)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Order Summary Calculation -->
                        <div class="order-summary-box">
                            <div class="summary-row">
                                <span>Unit Ticket Price:</span>
                                <span id="summaryUnitPrice">Rp 1,850,000</span>
                            </div>
                            <div class="summary-row">
                                <span>Quantity:</span>
                                <span id="summaryQty">1 Ticket</span>
                            </div>
                            <div class="summary-row">
                                <span>Taxes & Service Fee (5%):</span>
                                <span id="summaryTax">Rp 92,500</span>
                            </div>
                            <div class="summary-divider"></div>
                            <div class="summary-row total">
                                <span>TOTAL PAYMENT:</span>
                                <span id="summaryTotalPrice">Rp 1,942,500</span>
                            </div>
                        </div>

                        <!-- Terms & Conditions Agreement Checkbox -->
                        <div class="form-check">
                            <input type="checkbox" id="termsCheck" required>
                            <label for="termsCheck">
                                I confirm that all personal details entered above strictly match my valid government ID / Passport, and I accept the official terms of the CORTIS Concert World Tour ticketing policy.
                            </label>
                        </div>

                        <!-- Form Action Buttons -->
                        <div class="form-actions">
                            <button type="button" class="btn-cancel" onclick="closeTicketModal()">CANCEL</button>
                            <button type="submit" class="btn-submit" id="btnSubmitOrder">CONFIRM & ORDER TICKET</button>
                        </div>
                    </form>

                    <!-- 2. Order Success & Payment Instructions View -->
                    <div id="orderSuccessView" class="order-success-view" style="display: none;">
                        <div class="success-icon-badge">
                            <svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                        <h3 class="success-title">ORDER PLACED SUCCESSFULLY!</h3>
                        <p class="success-subtitle">Order ID: <span id="receiptOrderId" class="order-ref">#ORD-999</span> &bull; Status: <span class="badge-pending">WAITING PAYMENT</span></p>

                        <div class="receipt-card">
                            <div class="receipt-row">
                                <span class="receipt-label">Ticket Holder</span>
                                <strong id="receiptFullname">John Doe</strong>
                            </div>
                            <div class="receipt-row">
                                <span class="receipt-label">ID Number (NIK)</span>
                                <span id="receiptIdentity">6171012304000001</span>
                            </div>
                            <div class="receipt-row">
                                <span class="receipt-label">Contact / Email</span>
                                <span id="receiptContact">johndoe@example.com</span>
                            </div>
                            <div class="receipt-divider"></div>
                            <div class="receipt-row">
                                <span class="receipt-label">Tour & City</span>
                                <strong id="receiptConcert">JAKARTA (ICE BSD CITY)</strong>
                            </div>
                            <div class="receipt-row">
                                <span class="receipt-label">Tier & Quantity</span>
                                <span id="receiptCategory">Festival (1 Ticket)</span>
                            </div>
                            <div class="receipt-row">
                                <span class="receipt-label">Payment Gateway</span>
                                <span id="receiptPayment">Bank Transfer - BCA</span>
                            </div>
                            <div class="receipt-divider"></div>
                            <div class="receipt-row total">
                                <span class="receipt-label">AMOUNT DUE</span>
                                <strong id="receiptTotal" class="amount-due">Rp 1,942,500</strong>
                            </div>
                        </div>

                        <div class="payment-instructions">
                            <h4>PAYMENT INSTRUCTIONS</h4>
                            <p>Please complete payment to official Virtual Account Number below within <strong>15 minutes</strong>:</p>
                            <div class="va-display">
                                <code id="receiptVaNumber">8077 0812 3456 7890</code>
                            </div>
                            <small>Orders will be automatically verified upon receipt of funds. E-Ticket QR Code is sent via Email / WhatsApp.</small>
                        </div>

                        <div class="success-actions">
                            <button type="button" class="btn-done" onclick="finishOrder()">CLOSE & RETURN TO SCHEDULE</button>
                        </div>
                    </div>
                </div>
            </div>

        </main>

        <!-- FOOTER -->
        <footer>
            <div class="footer-social">
                <a href="https://www.instagram.com/cortis/" target="_blank"
                    style="display: flex; align-items: center; gap: 8px; text-decoration: none; color: inherit; transition: opacity 0.2s;"><img src="images/instagram.png" alt="Instagram"
                        class="footer-icon" style="filter: none;"> INSTAGRAM</a>
                <a href="https://www.tiktok.com/@cortis_bighit" target="_blank"
                    style="display: flex; align-items: center; gap: 8px; text-decoration: none; color: inherit; transition: opacity 0.2s;"><img src="images/tiktok.png" alt="TikTok"
                        class="footer-icon" style="filter: none;"> TIKTOK</a>
                <a href="https://x.com/cortis_bighit" target="_blank"
                    style="display: flex; align-items: center; gap: 8px; text-decoration: none; color: inherit; transition: opacity 0.2s;"><img src="images/x.png" alt="X"
                        class="footer-icon" style="filter: none;" > X</a>
                <a href="https://youtube.com/@cortis_bighit?si=zvE_dOeq7-w3zoBZ" target="_blank"
                    style="display: flex; align-items: center; gap: 8px; text-decoration: none; color: inherit; transition: opacity 0.2s;"
                    onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">
                    <img src="images/Youtube.png" alt="Youtube" class="footer-icon" style="filter: none;"> YOUTUBE
                </a>
            </div>
            <div class="footer-bottom">
                &copy; 2024 CORTIS OFFICIAL. PRIVACY | CONTACT
            </div>
        </footer>

    </div>

    <script>
        const concertMap = <?php 
            $cMap = [];
            foreach ($concerts as $c) {
                $cMap[$c['id']] = [
                    'location' => $c['location'],
                    'venue' => $c['venue'],
                    'date' => $c['concert_date']
                ];
            }
            echo json_encode($cMap);
        ?>;

        let currentOrderCounter = 1001;

        function openTicketModal(concertId, location, venue, date) {
            const selectEl = document.getElementById('concert_id');
            if (selectEl && selectEl.querySelector(`option[value="${concertId}"]`)) {
                selectEl.value = concertId;
            }

            document.getElementById('displayConcertLocation').textContent = location;
            document.getElementById('displayConcertVenue').textContent = venue;
            document.getElementById('displayConcertDate').textContent = date;

            document.getElementById('ticketOrderForm').style.display = 'block';
            document.getElementById('orderSuccessView').style.display = 'none';

            calculateOrderTotal();

            document.getElementById('ticketModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeTicketModal() {
            document.getElementById('ticketModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function onConcertChange(concertId) {
            const info = concertMap[concertId];
            if (info) {
                document.getElementById('displayConcertLocation').textContent = info.location;
                document.getElementById('displayConcertVenue').textContent = info.venue;
                document.getElementById('displayConcertDate').textContent = info.date;
            }
        }

        function changeQty(delta) {
            const qtyInput = document.getElementById('quantity');
            let current = parseInt(qtyInput.value) || 1;
            current += delta;
            if (current < 1) current = 1;
            if (current > 4) current = 4;
            qtyInput.value = current;
            calculateOrderTotal();
        }

        function calculateOrderTotal() {
            const catSelect = document.getElementById('ticket_category');
            const selectedOpt = catSelect.options[catSelect.selectedIndex];
            const price = parseInt(selectedOpt.getAttribute('data-price')) || 1850000;
            const qty = parseInt(document.getElementById('quantity').value) || 1;

            const subtotal = price * qty;
            const tax = Math.round(subtotal * 0.05);
            const total = subtotal + tax;

            document.getElementById('summaryUnitPrice').textContent = 'Rp ' + price.toLocaleString('id-ID');
            document.getElementById('summaryQty').textContent = qty + (qty > 1 ? ' Tickets' : ' Ticket');
            document.getElementById('summaryTax').textContent = 'Rp ' + tax.toLocaleString('id-ID');
            document.getElementById('summaryTotalPrice').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        async function handleTicketOrder(e) {
            e.preventDefault();

            const form = document.getElementById('ticketOrderForm');
            const submitBtn = document.getElementById('btnSubmitOrder');
            submitBtn.disabled = true;
            submitBtn.textContent = 'PROCESSING ORDER...';

            const concertId = document.getElementById('concert_id').value;
            const concertInfo = concertMap[concertId] || { location: 'JAKARTA', venue: 'ICE BSD', date: '2026-08-31' };
            const fullname = document.getElementById('fullname').value.trim();
            const identityNo = document.getElementById('identity_no').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const catSelect = document.getElementById('ticket_category');
            const category = catSelect.value;
            const qty = document.getElementById('quantity').value;
            const payment = document.getElementById('payment_method').value;
            const totalText = document.getElementById('summaryTotalPrice').textContent;

            const vaNum = '8077' + phone.replace(/[^0-9]/g, '').slice(-10);

            const formData = new FormData(form);

            try {
                const response = await fetch('process_order.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    document.getElementById('receiptOrderId').textContent = '#ORD-' + result.order_id;
                } else {
                    console.warn('Backend note:', result.message);
                    document.getElementById('receiptOrderId').textContent = '#ORD-' + currentOrderCounter++;
                }
            } catch (err) {
                console.info('Backend fetch notice:', err);
                document.getElementById('receiptOrderId').textContent = '#ORD-' + currentOrderCounter++;
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'CONFIRM & ORDER TICKET';
            }

            document.getElementById('receiptFullname').textContent = fullname;
            document.getElementById('receiptIdentity').textContent = identityNo;
            document.getElementById('receiptContact').textContent = email + ' / ' + phone;
            document.getElementById('receiptConcert').textContent = concertInfo.location + ' (' + concertInfo.venue + ') — ' + concertInfo.date;
            document.getElementById('receiptCategory').textContent = category + ' (' + qty + (qty > 1 ? ' Tickets' : ' Ticket') + ')';
            document.getElementById('receiptPayment').textContent = payment;
            document.getElementById('receiptTotal').textContent = totalText;
            document.getElementById('receiptVaNumber').textContent = vaNum;

            form.style.display = 'none';
            document.getElementById('orderSuccessView').style.display = 'block';
        }

        function finishOrder() {
            document.getElementById('ticketOrderForm').reset();
            document.getElementById('quantity').value = 1;
            closeTicketModal();
        }

        window.addEventListener('click', (e) => {
            const modal = document.getElementById('ticketModal');
            if (e.target === modal) {
                closeTicketModal();
            }
        });

        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeTicketModal();
            }
        });

        const menuToggle = document.getElementById('menuToggle');
        const navMenu = document.getElementById('navMenu');

        menuToggle.addEventListener('click', () => {
            menuToggle.classList.toggle('active');
            navMenu.classList.toggle('active');
            document.body.classList.toggle('no-scroll');
        });

        const navLinks = document.querySelectorAll('.nav-menu a');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                menuToggle.classList.remove('active');
                navMenu.classList.remove('active');
                document.body.classList.remove('no-scroll');
            });
        });
    </script>
</body>

</html>
