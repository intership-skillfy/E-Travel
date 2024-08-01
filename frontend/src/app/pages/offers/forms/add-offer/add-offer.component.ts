import { Component,Input, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';


@Component({
  selector: 'app-add-offer',
  templateUrl: './add-offer.component.html',
  styleUrls: ['./add-offer.component.scss']
})
export class AddOfferComponent implements OnInit{
  type: string = '';

  constructor(private route: ActivatedRoute) {}

  ngOnInit(): void {
    this.route.paramMap.subscribe(params => {
      this.type = params.get('type') || '';
    });
  }

  onSubmit(form: any) {
    console.log('Form submitted', form, 'Type:', this.type);
    // Handle form submission
  }

}
