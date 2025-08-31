<footer class="main-footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="footer-brand">
                    <a href="{{ route('home') }}" class="logo">
                        <i class="fas fa-paw"></i>
                        Pawtel
                    </a>
                    <p class="mt-3">
                        Premium cat hotel and healthcare services for your beloved feline friends.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="footer-heading">Our Services</h5>
                <ul class="footer-links">
                    <li><a href="{{ route('booking.hotel.index') }}"><i class="fas fa-paw"></i> Cat Hotel & Boarding</a>
                    </li>
                    <li><a href="{{ route('booking.spa.index') }}"><i class="fas fa-spa"></i> Spa & Grooming</a></li>
                    <li><a href="{{ route('booking.spay.index') }}"><i class="fas fa-stethoscope"></i> Spay/Neuter
                            Services</a></li>
                    <li><a href="{{ route('shop.index') }}"><i class="fas fa-shopping-cart"></i> Pet Supplies</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6 mb-4">
                <h5 class="footer-heading">Quick Links</h5>
                <ul class="footer-links">
                    <li><a href="{{ route('community.posts') }}">Community</a></li>
                    <li><a href="{{ route('community.adoption') }}">Adoption</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>

            <div class="col-lg-3 mb-4">
                <h5 class="footer-heading">Contact Info</h5>
                <div class="contact-info">
                    <div class="contact-item">
                        <div class="contact-icon"><i class="fas fa-phone"></i></div>
                        <div>
                            <div>+8801733191556</div>
                            <small>(Appointment required)</small>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div>236/5/A Rahman mansion<br>60ft road, Mirpur, Dhaka</div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon"><i class="fas fa-clock"></i></div>
                        <div>Open: 10AM - 7PM<br>Friday: Closed</div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="footer-divider">

        <div class="row">
            <div class="col-12 text-center">
                <p class="footer-copyright">
                    &copy; {{ date('Y') }} Pawtel. All rights reserved. | Fur Babies Safety
                </p>
            </div>
        </div>
    </div>
</footer>
