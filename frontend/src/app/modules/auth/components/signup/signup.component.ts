import { Component } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-signup',
  templateUrl: './signup.component.html',
  styleUrls: ['./signup.component.scss']
})
export class SignupComponent {

  constructor(private router: Router) {}

  navigateTo(role: string) {
    if (role === 'agency') {
      this.router.navigate(['/auth/agencyRegistration']);
    } else if (role === 'user') {
      this.router.navigate(['/auth/clientRegistration']);
    }
  }
}
