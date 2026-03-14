<?php
require_once __DIR__.'/../config/helpers.php';$page_title='Contact Us';
require_once __DIR__.'/../includes/header.php';require_once __DIR__.'/../includes/navbar.php';
?>
<section class="pt-28 pb-20 bg-gray-50">
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
  <div class="text-center mb-16" data-aos="fade-up"><h1 class="text-3xl sm:text-4xl font-extrabold mb-4 font-heading">Get In <span class="grad-text">Touch</span></h1><p class="text-gray-500">Have questions? We'd love to hear from you.</p></div>
  <div class="grid md:grid-cols-3 gap-8 mb-12"><?php
  $ci=[['map-pin','Address',get_setting('contact_address','Nairobi, Kenya')],['phone','Phone',get_setting('contact_phone','+254 700 000 000')],['mail','Email',get_setting('contact_email','info@fpesa.co.ke')]];
  foreach($ci as $i=>$c):?><div class="text-center p-8 bg-white rounded-2xl border border-gray-100 card-lift" data-aos="fade-up" data-aos-delay="<?=$i*100?>"><div class="icon-box w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4 text-primary"><i data-lucide="<?=$c[0]?>" class="w-6 h-6"></i></div><h3 class="font-bold mb-1 font-heading"><?=$c[1]?></h3><p class="text-sm text-gray-500"><?=e($c[2])?></p></div><?php endforeach;?></div>
  <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 max-w-2xl mx-auto" data-aos="fade-up">
    <h2 class="font-bold text-xl mb-6 font-heading">Send Us a Message</h2>
    <form class="space-y-4" onsubmit="event.preventDefault();alert('Message sent! We will get back to you.');this.reset();">
      <div class="grid sm:grid-cols-2 gap-4"><div><label class="text-sm font-semibold mb-1 block">Name</label><input type="text" required class="finput w-full py-3 px-4 rounded-xl border border-gray-200 text-sm" placeholder="Your name"></div><div><label class="text-sm font-semibold mb-1 block">Email</label><input type="email" required class="finput w-full py-3 px-4 rounded-xl border border-gray-200 text-sm" placeholder="you@example.com"></div></div>
      <div><label class="text-sm font-semibold mb-1 block">Subject</label><input type="text" required class="finput w-full py-3 px-4 rounded-xl border border-gray-200 text-sm" placeholder="How can we help?"></div>
      <div><label class="text-sm font-semibold mb-1 block">Message</label><textarea rows="4" required class="finput w-full py-3 px-4 rounded-xl border border-gray-200 text-sm" placeholder="Your message..."></textarea></div>
      <button type="submit" class="btn-primary px-8 py-3 rounded-xl text-sm font-bold">Send Message</button>
    </form>
  </div>
</div></section>
<?php require_once __DIR__.'/../includes/footer.php';?>
