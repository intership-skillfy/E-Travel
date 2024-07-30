import { TestBed } from '@angular/core/testing';

import { ExursionsService } from './exursions.service';

describe('ExursionsService', () => {
  let service: ExursionsService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(ExursionsService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
