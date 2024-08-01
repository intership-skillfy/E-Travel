import { TestBed } from '@angular/core/testing';

import { ExcursionsService } from './excursions.service';

describe('ExcursionsService', () => {
  let service: ExcursionsService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(ExcursionsService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
