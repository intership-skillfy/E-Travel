import { TestBed } from '@angular/core/testing';

import { OfferserviceService } from './offerservice.service';

describe('OfferserviceService', () => {
  let service: OfferserviceService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(OfferserviceService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
