
    <h2>Select Payment Method</h2>
    <form id="paymentForm" action="process_payment.php" method="post">
        <input type="radio" id="cardPayment" name="paymentMethod" value="Card Payment">
        <label for="cardPayment">Card Payment</label><br>
        <input type="radio" id="upiPayment" name="paymentMethod" value="UPI">
        <label for="upiPayment">UPI</label><br>
        <input type="radio" id="netbankingPayment" name="paymentMethod" value="NetBanking">
        <label for="netbankingPayment">NetBanking</label><br><br>
        <button type="submit" id="completePaymentBtn" disabled>Complete Payment</button>
    </form>

    <script>
        // Enable "Complete Payment" button when a payment method is selected
        const paymentForm = document.getElementById('paymentForm');
        const completePaymentBtn = document.getElementById('completePaymentBtn');
        const paymentMethodInputs = document.querySelectorAll('input[name="paymentMethod"]');

        paymentMethodInputs.forEach(input => {
            input.addEventListener('change', function() {
                completePaymentBtn.disabled = false;
            });
        });
    </script>
